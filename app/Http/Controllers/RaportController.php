<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\RaportBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RaportController extends Controller {
    public function __invoke(Request $request, RaportBuilder $builder): View {
        $murid = Auth::user()?->profilMurid;

        abort_unless($murid, 403, 'Halaman ini hanya untuk murid.');

        $semester = (int) $request->query('semester', '1');
        $semester = in_array($semester, [1, 2], true) ? $semester : 1;

        $tahunAjaran = (string) $request->query('tahun', $this->currentTahunAjaran());
        if (!preg_match('/^\d{4}\/\d{4}$/', $tahunAjaran)) {
            $tahunAjaran = $this->currentTahunAjaran();
        }

        $records = $builder->records($murid, $semester, $tahunAjaran);

        return view('raport-print', [
            'student' => $builder->studentInfo($murid, $tahunAjaran),
            'records' => $records,
            'summary' => $builder->summary($records),
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
            'tanggalCetak' => now()->locale('id')->translatedFormat('d F Y'),
        ]);
    }

    private function currentTahunAjaran(): string {
        $year = (int) now()->format('Y');
        $month = (int) now()->format('m');

        return $month >= 7 ? $year.'/'.($year + 1) : ($year - 1).'/'.$year;
    }
}
