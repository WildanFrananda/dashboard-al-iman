<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Subject;
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
        $query = Subject::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('subject_name', 'like', '%'.$this->search.'%')
                    ->orWhere('subject_code', 'like', '%'.$this->search.'%');
            });
        }

        $subjects = $query->latest()->paginate(10);

        return view('livewire.manage-subject', [
            'subjects' => $subjects,
        ]);
    }
}
