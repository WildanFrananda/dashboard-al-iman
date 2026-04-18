<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Kelas;
use App\Models\ProfilGuru;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage Jadwal - SIAKMAN')]
class ManageSchedule extends Component {
    use WithPagination;

    public $search = '';

    // Form fields
    public $guru_id = '';

    public $subject_id = '';

    public $kelas_id = '';

    public $hari = '';

    public $jam_mulai = '';

    public $jam_selesai = '';

    // Edit state
    public $editingId = null;

    public $showForm = false;

    public $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function mount() {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
    }

    public function updatedSearch() {
        $this->resetPage();
    }

    public function openForm() {
        $this->resetForm();
        $this->showForm = true;
    }

    public function closeForm() {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm() {
        $this->guru_id = '';
        $this->subject_id = '';
        $this->kelas_id = '';
        $this->hari = '';
        $this->jam_mulai = '';
        $this->jam_selesai = '';
        $this->editingId = null;
    }

    public function save() {
        $this->validate([
            'guru_id' => 'required|exists:profil_guru,id',
            'subject_id' => 'required|exists:subjects,id',
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        TeachingSchedule::updateOrCreate(
            ['id' => $this->editingId],
            [
                'guru_id' => $this->guru_id,
                'subject_id' => $this->subject_id,
                'kelas_id' => $this->kelas_id,
                'hari' => $this->hari,
                'jam_mulai' => $this->jam_mulai,
                'jam_selesai' => $this->jam_selesai,
            ]
        );

        session()->flash('message', $this->editingId ? 'Jadwal berhasil diupdate.' : 'Jadwal berhasil ditambahkan.');
        $this->closeForm();
    }

    public function edit($id) {
        $schedule = TeachingSchedule::findOrFail($id);
        $this->editingId = $schedule->id;
        $this->guru_id = $schedule->guru_id;
        $this->subject_id = $schedule->subject_id;
        $this->kelas_id = $schedule->kelas_id;
        $this->hari = $schedule->hari;
        $this->jam_mulai = $schedule->jam_mulai ? Carbon::parse($schedule->jam_mulai)->format('H:i') : '';
        $this->jam_selesai = $schedule->jam_selesai ? Carbon::parse($schedule->jam_selesai)->format('H:i') : '';
        $this->showForm = true;
    }

    public function delete($id): void {
        TeachingSchedule::findOrFail($id)->delete();
        session()->flash('message', 'Jadwal berhasil dihapus.');
    }

    public function render() {
        $schedules = TeachingSchedule::query()
            ->with(['guru', 'subject', 'kelas'])
            ->when($this->search, function ($query) {
                $query->whereHas('guru', function ($q) {
                    $q->where('nama_lengkap', 'ilike', "%{$this->search}%");
                })
                    ->orWhereHas('subject', function ($q) {
                        $q->where('subject_name', 'ilike', "%{$this->search}%");
                    })
                    ->orWhereHas('kelas', function ($q) {
                        $q->where('nama_kelas', 'ilike', "%{$this->search}%");
                    })
                    ->orWhere('hari', 'ilike', "%{$this->search}%");
            })
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(10);

        $gurus = ProfilGuru::orderBy('nama_lengkap')->get();
        $subjects = Subject::orderBy('subject_name')->get();
        $classes = Kelas::orderBy('nama_kelas')->get();

        return view('livewire.manage-schedule', [
            'schedules' => $schedules,
            'gurus' => $gurus,
            'subjects' => $subjects,
            'classes' => $classes,
        ]);
    }
}
