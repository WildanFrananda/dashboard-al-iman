<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Kelas;
use App\Models\ProfilGuru;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage Class - SIAKMAN')]
class ManageClass extends Component {
    use WithPagination;

    public $search = '';

    // Form fields
    public $kode_kelas = '';

    public $nama_kelas = '';

    public $tahun_ajaran = '';

    public $wali_kelas_id = null;

    // Edit state
    public $editingId = null;

    public $showForm = false;

    public $confirmingDelete = null;

    public function mount() {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        $this->tahun_ajaran = date('Y').'/'.(date('Y') + 1);
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
        $this->kode_kelas = '';
        $this->nama_kelas = '';
        $this->tahun_ajaran = date('Y').'/'.(date('Y') + 1);
        $this->wali_kelas_id = null;
        $this->editingId = null;
    }

    public function save() {
        $this->validate([
            'kode_kelas' => 'required|string|max:20|unique:kelas,kode_kelas,'.$this->editingId,
            'nama_kelas' => 'required|string|max:100',
            'tahun_ajaran' => 'required|string|max:20',
            'wali_kelas_id' => 'nullable|exists:profil_guru,id',
        ]);

        Kelas::updateOrCreate(
            ['id' => $this->editingId],
            [
                'kode_kelas' => $this->kode_kelas,
                'nama_kelas' => $this->nama_kelas,
                'tahun_ajaran' => $this->tahun_ajaran,
                'wali_kelas_id' => $this->wali_kelas_id ?: null,
            ]
        );

        session()->flash('message', $this->editingId ? 'Kelas berhasil diupdate.' : 'Kelas berhasil ditambahkan.');
        $this->closeForm();
    }

    public function edit($id) {
        $kelas = Kelas::findOrFail($id);
        $this->editingId = $kelas->id;
        $this->kode_kelas = $kelas->kode_kelas;
        $this->nama_kelas = $kelas->nama_kelas;
        $this->tahun_ajaran = $kelas->tahun_ajaran;
        $this->wali_kelas_id = $kelas->wali_kelas_id;
        $this->showForm = true;
    }

    public function confirmDelete($id) {
        $this->confirmingDelete = $id;
    }

    public function delete($id) {
        Kelas::findOrFail($id)->delete();
        $this->confirmingDelete = null;
        session()->flash('message', 'Kelas berhasil dihapus.');
    }

    public function render() {
        $classes = Kelas::query()
            ->with('waliKelas')
            ->when($this->search, function ($query) {
                $query->where('kode_kelas', 'ilike', "%{$this->search}%")
                    ->orWhere('nama_kelas', 'ilike', "%{$this->search}%")
                    ->orWhereHas('waliKelas', function ($q) {
                        $q->where('nama_lengkap', 'ilike', "%{$this->search}%");
                    });
            })
            ->orderBy('kode_kelas')
            ->paginate(10);

        $gurus = ProfilGuru::orderBy('nama_lengkap')->get();

        return view('livewire.manage-class', [
            'classes' => $classes,
            'gurus' => $gurus,
        ]);
    }
}
