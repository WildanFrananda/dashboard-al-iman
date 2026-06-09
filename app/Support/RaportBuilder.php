<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Absensi;
use App\Models\Nilai;
use App\Models\ProfilMurid;

/**
 * Membangun data rapor (identitas, baris nilai, ringkasan) untuk seorang murid.
 * Dipakai bersama oleh StudentGrade (Livewire) dan RaportController (cetak/PDF).
 */
class RaportBuilder {
    public function studentInfo(ProfilMurid $murid, string $tahunAjaran): array {
        $kelas = $murid->kelas()
            ->wherePivot('tahun_ajaran', $tahunAjaran)
            ->first()
            ?? $murid->kelas()->orderByPivot('created_at', 'desc')->first();

        return [
            'nama' => $murid->nama_lengkap ?? $murid->user?->name,
            'nis' => $murid->nis,
            'kelas' => $kelas?->nama_kelas,
        ];
    }

    /**
     * Satu baris per mata pelajaran: subject, kkm, kehadiran, uts, uas, nilai_akhir, keterangan.
     *
     * @return array<int, array<string, mixed>>
     */
    public function records(ProfilMurid $murid, int $semester, string $tahunAjaran): array {
        $nilais = Nilai::where('murid_id', $murid->id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->with('subject')
            ->orderBy('subject_id')
            ->get();

        $attendance = $this->attendanceBySubject($murid->id);

        $records = [];
        foreach ($nilais->groupBy('subject_id') as $subjectId => $subjectNilais) {
            $uts = $subjectNilais->firstWhere('tipe_nilai', 'UTS');
            $uas = $subjectNilais->firstWhere('tipe_nilai', 'UAS');

            $values = array_filter([$uts?->nilai, $uas?->nilai], fn ($v) => $v !== null);
            $nilaiAkhir = count($values) > 0 ? (int) round(array_sum($values) / count($values)) : null;

            $records[] = [
                'subject' => $subjectNilais->first()->subject?->subject_name ?? '-',
                'kkm' => $uts?->kkm ?? $uas?->kkm ?? 75,
                'kehadiran' => $attendance[(int) $subjectId] ?? null,
                'uts' => $uts?->nilai,
                'uas' => $uas?->nilai,
                'nilai_akhir' => $nilaiAkhir,
                'keterangan' => $uts?->keterangan ?? $uas?->keterangan ?? null,
            ];
        }

        return $records;
    }

    /**
     * @param  array<int, array<string, mixed>>  $records
     */
    public function summary(array $records): array {
        $utsValues = array_filter(array_column($records, 'uts'), fn ($v) => $v !== null);
        $uasValues = array_filter(array_column($records, 'uas'), fn ($v) => $v !== null);

        return [
            'rata_uts' => count($utsValues) > 0 ? round(array_sum($utsValues) / count($utsValues), 1) : null,
            'rata_uas' => count($uasValues) > 0 ? round(array_sum($uasValues) / count($uasValues), 1) : null,
            'mapel' => count($records),
        ];
    }

    /**
     * Persentase kehadiran (Hadir / total) per subject_id.
     *
     * @return array<int, int|null>
     */
    private function attendanceBySubject(int $muridId): array {
        $absensi = Absensi::where('murid_id', $muridId)
            ->with('pertemuanKelas.teachingSchedule:id,subject_id')
            ->get()
            ->groupBy(fn ($a) => $a->pertemuanKelas?->teachingSchedule?->subject_id);

        $map = [];
        foreach ($absensi as $subjectId => $rows) {
            if ($subjectId === null || $subjectId === '') {
                continue;
            }
            $total = $rows->count();
            $hadir = $rows->where('status_kehadiran', 'Hadir')->count();
            $map[(int) $subjectId] = $total > 0 ? (int) round($hadir / $total * 100) : null;
        }

        return $map;
    }
}
