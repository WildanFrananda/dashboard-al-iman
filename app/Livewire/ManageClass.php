<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage Class - SIAKMAN')]
class ManageClass extends Component {
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
        // Since there is no Kelas model yet, this is dummy data representing typical school classes
        $allClasses = collect([
            (object) ['id' => 1, 'kode_kelas' => 'X-MIPA-1', 'nama_kelas' => '10 MIPA 1', 'wali_kelas' => 'Budi Santoso'],
            (object) ['id' => 2, 'kode_kelas' => 'X-MIPA-2', 'nama_kelas' => '10 MIPA 2', 'wali_kelas' => 'Siti Aisyah'],
            (object) ['id' => 3, 'kode_kelas' => 'X-IPS-1',  'nama_kelas' => '10 IPS 1',  'wali_kelas' => 'Agus Rahman'],
            (object) ['id' => 4, 'kode_kelas' => 'X-IPS-2',  'nama_kelas' => '10 IPS 2',  'wali_kelas' => 'Diana Putri'],
            (object) ['id' => 5, 'kode_kelas' => 'XI-MIPA-1', 'nama_kelas' => '11 MIPA 1', 'wali_kelas' => 'Bambang Pamungkas'],
            (object) ['id' => 6, 'kode_kelas' => 'XI-IPS-1', 'nama_kelas' => '11 IPS 1', 'wali_kelas' => 'Rini Susanti'],
        ]);

        $filteredClasses = $allClasses->filter(function ($item) {
            if ($this->search === '') {
                return true;
            }
            return stripos($item->kode_kelas, $this->search) !== false || 
                   stripos($item->nama_kelas, $this->search) !== false ||
                   stripos($item->wali_kelas, $this->search) !== false;
        });

        // Simulating pagination
        $classes = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredClasses->values(),
            $filteredClasses->count(),
            10,
            1,
            ['path' => route('manage-class')]
        );

        return view('livewire.manage-class', [
            'classes' => $classes,
        ]);
    }
}
