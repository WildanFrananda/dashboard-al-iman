<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Absensi Pengajar - SIAKMAN')]
class TeacherAttendance extends Component {
    public $teacherName = '';
    public $classId = '';
    public $date = '';
    public $students = [];

    public function mount() {
        // Data awal (dummy) yang disesuaikan dengan desain pada gambar
        $this->students = [
            [
                'id' => 1,
                'name' => 'Alila Nafisah',
                'status' => 'Hadir'
            ],
            [
                'id' => 2,
                'name' => 'Khairril Anwar',
                'status' => 'Hadir'
            ],
        ];
    }

    public function submit() {
        // Logika untuk menyimpan data absensi
        // dd($this->teacherName, $this->classId, $this->date, $this->students);
        
        session()->flash('message', 'Data absensi berhasil disimpan.');
    }

    public function render() {
        return view('livewire.teacher-attendance');
    }
}
