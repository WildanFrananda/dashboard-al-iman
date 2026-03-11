<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\MataPelajaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage Subject - SIAKMAN')]
class ManageSubject extends Component {
    use WithPagination;

    public $search = '';

    public function mount() {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
    }

    public function updatedSearch() {
        $this->resetPage();
    }

    public function render() {
        $query = MataPelajaran::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('nama_mapel', 'like', '%'.$this->search.'%')
                    ->orWhere('kode_mapel', 'like', '%'.$this->search.'%');
            });
        }

        $pelajarans = $query->latest()->paginate(10);

        return view('livewire.manage-subject', [
            'pelajarans' => $pelajarans,
        ]);
    }
}
