<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Absensi - SIAKMAN')]
class Attendance extends Component
{
    public $summary = [];
    public $attendanceRecords = [];

    public function mount()
    {
        // 1. Data Ringkasan (Kartu Atas)
        $this->summary = [
            'hadir' => 10,
            'izin' => 1,
            'sakit' => 2,
            'alfa' => 1,
        ];

        // 2. Data Riwayat Per Mata Pelajaran (Table)
        // Format status: H=Hadir, I=Izin, S=Sakit, A=Alfa
        $this->attendanceRecords = [
            [
                'subject' => 'Bahasa Indonesia',
                'history' => [
                    ['meet' => 1, 'status' => 'H'],
                    ['meet' => 2, 'status' => 'H'],
                    ['meet' => 3, 'status' => 'H'],
                    ['meet' => 4, 'status' => 'H'],
                    ['meet' => 5, 'status' => 'A'], // Merah
                    ['meet' => 6, 'status' => 'H'],
                    ['meet' => 7, 'status' => 'H'],
                ]
            ],
            [
                'subject' => 'Matematika',
                'history' => [
                    ['meet' => 1, 'status' => 'H'],
                    ['meet' => 2, 'status' => 'H'],
                    ['meet' => 3, 'status' => 'H'],
                    ['meet' => 4, 'status' => 'H'],
                    ['meet' => 5, 'status' => 'I'], // Biru
                    ['meet' => 6, 'status' => 'H'],
                    ['meet' => 7, 'status' => 'H'],
                ]
            ],
            [
                'subject' => 'Bahasa Sunda',
                'history' => [
                    ['meet' => 1, 'status' => 'H'],
                    ['meet' => 2, 'status' => 'H'],
                    ['meet' => 3, 'status' => 'H'],
                    ['meet' => 4, 'status' => 'H'],
                    ['meet' => 5, 'status' => 'A'],
                    ['meet' => 6, 'status' => 'H'],
                    ['meet' => 7, 'status' => 'H'],
                ]
            ],
            [
                'subject' => 'PJOK',
                'history' => [
                    ['meet' => 1, 'status' => 'H'],
                    ['meet' => 2, 'status' => 'H'],
                    ['meet' => 3, 'status' => 'H'],
                    ['meet' => 4, 'status' => 'H'],
                    ['meet' => 5, 'status' => 'A'],
                    ['meet' => 6, 'status' => 'H'],
                    ['meet' => 7, 'status' => 'H'],
                ]
            ],
             [
                'subject' => 'Pendidikan Kewarnegaraan',
                'history' => [
                    ['meet' => 1, 'status' => 'H'],
                    ['meet' => 2, 'status' => 'H'],
                    ['meet' => 3, 'status' => 'H'],
                    ['meet' => 4, 'status' => 'H'],
                    ['meet' => 5, 'status' => 'S'], // Kuning
                    ['meet' => 6, 'status' => 'H'],
                    ['meet' => 7, 'status' => 'H'],
                ]
            ],
        ];
    }

    public function render()
    {
        return view('livewire.attendance');
    }
}