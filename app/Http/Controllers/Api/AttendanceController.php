<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfilMurid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Get attendance summary for the logged-in student.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function studentDashboard(Request $request): JsonResponse
    {
        // 1. Identify the logged-in student via the user -> profil_murid relationship.
        $user = Auth::user();

        if (!$user || $user->role !== 'murid') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access or user is not a student.',
                'data' => null
            ], 403);
        }

        $studentProfile = ProfilMurid::where('user_id', $user->id)->first();

        if (!$studentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found.',
                'data' => null
            ], 404);
        }

        // 2. Query the attendance data with relations
        // We load the attendance records along with the class meeting and schedule to get the subject.
        $attendances = $studentProfile->absensis()
            ->with(['pertemuanKelas.jadwalMengajar.mataPelajaran'])
            ->get();

        // 3. Group by mata_pelajaran
        $groupedData = [];

        foreach ($attendances as $attendance) {
            $pertemuan = $attendance->pertemuanKelas;
            if (!$pertemuan || !$pertemuan->jadwalMengajar || !$pertemuan->jadwalMengajar->mataPelajaran) {
                continue;
            }

            $mapel = $pertemuan->jadwalMengajar->mataPelajaran;
            $mapelId = $mapel->id;

            if (!isset($groupedData[$mapelId])) {
                $groupedData[$mapelId] = [
                    'id_mapel' => $mapel->id,
                    'kode_mapel' => $mapel->kode_mapel,
                    'nama_mapel' => $mapel->nama_mapel,
                    'total_pertemuan' => 0,
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpa' => 0,
                ];
            }

            $groupedData[$mapelId]['total_pertemuan'] += 1;

            $status = strtolower($attendance->status_kehadiran);
            if (isset($groupedData[$mapelId][$status])) {
                $groupedData[$mapelId][$status] += 1;
            }
        }

        // 4. Calculate persentase_kehadiran and format the final array
        $result = [];
        foreach ($groupedData as $data) {
            $totalKehadiran = $data['hadir'];
            $totalPertemuan = $data['total_pertemuan'];
            
            $persentase = $totalPertemuan > 0 
                ? round(($totalKehadiran / $totalPertemuan) * 100, 2) 
                : 0;

            $data['persentase_kehadiran'] = $persentase;
            $result[] = $data;
        }

        // Return a clean, mobile-friendly JSON response
        return response()->json([
            'success' => true,
            'message' => 'Student attendance dashboard retrieved successfully.',
            'data' => [
                'student_name' => $studentProfile->nama_lengkap,
                'nis' => $studentProfile->nis,
                'attendance_summary' => $result
            ]
        ], 200);
    }
}
