<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\AcademicEvent;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use App\Models\SchoolSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard - SIAKMAN')]
class Dashboard extends Component {
    // Dummy Data Properties
    public $stats = [];

    public $schedule = [];

    public $attendanceData = [];

    public function mount() {
        // 1. Data Statistik (Card Atas)
        $this->stats = [
            [
                'title' => 'Jumlah Murid',
                'value' => (string) ProfilMurid::count(),
                'icon_bg' => 'bg-blue-100',
                'icon_color' => 'text-blue-600',
                'icon' => 'users', // Nanti mapping di view
            ],
            [
                'title' => 'Jumlah Tenaga Pengajar',
                'value' => (string) ProfilGuru::count(),
                'icon_bg' => 'bg-green-100',
                'icon_color' => 'text-green-600',
                'icon' => 'teacher',
            ],
            [
                'title' => 'Acara Mendatang',
                'value' => (string) AcademicEvent::upcoming()->count(),
                'icon_bg' => 'bg-pink-100',
                'icon_color' => 'text-pink-600',
                'icon' => 'calendar',
            ],
            [
                'title' => 'Jumlah Ekstrakurikuler',
                'value' => SchoolSetting::get('jumlah_ekskul', '0'),
                'icon_bg' => 'bg-orange-100',
                'icon_color' => 'text-orange-600',
                'icon' => 'target',
            ],
            [
                'title' => 'Tingkat Akreditasi',
                'value' => SchoolSetting::get('tingkat_akreditasi', '-'),
                'icon_bg' => 'bg-yellow-100',
                'icon_color' => 'text-yellow-600',
                'icon' => 'medal',
            ],
        ];

        // 2. Data Jadwal (Card Tengah)
        $this->schedule = [
            [
                'day' => 'Senin',
                'lessons' => [
                    ['subject' => 'Bahasa Indonesia', 'teacher' => 'Rina Nurdiana, S.Pd', 'time' => '10:00 - 10:30'],
                    ['subject' => 'Matematika', 'teacher' => 'Rina Nurdiana, S.Pd', 'time' => '10:30 - 11:00'],
                ],
            ],
            [
                'day' => 'Senin', // Duplikasi sesuai gambar untuk efek scroll/grid
                'lessons' => [
                    ['subject' => 'Bahasa Indonesia', 'teacher' => 'Rina Nurdiana, S.Pd', 'time' => '10:00 - 10:30'],
                    ['subject' => 'Matematika', 'teacher' => 'Rina Nurdiana, S.Pd', 'time' => '10:30 - 11:00'],
                ],
            ],
            [
                'day' => 'Senin',
                'lessons' => [
                    ['subject' => 'Bahasa Indonesia', 'teacher' => 'Rina Nurdiana, S.Pd', 'time' => '10:00 - 10:30'],
                    ['subject' => 'Matematika', 'teacher' => 'Rina Nurdiana, S.Pd', 'time' => '10:30 - 11:00'],
                ],
            ],
            [
                'day' => 'Senin',
                'lessons' => [
                    ['subject' => 'Bahasa Indonesia', 'teacher' => 'Rina Nurdiana, S.Pd', 'time' => '10:00 - 10:30'],
                    ['subject' => 'Matematika', 'teacher' => 'Rina Nurdiana, S.Pd', 'time' => '10:30 - 11:00'],
                ],
            ],
        ];
    }

    public function render() {
        return view('livewire.dashboard');
    }
}
