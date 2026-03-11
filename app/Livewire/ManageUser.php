<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage User - SIAKMAN')]
class ManageUser extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRole = 'all';

    public function mount()
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access - khusus admin');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedRole()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        if ($this->search !== '') {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedRole !== 'all') {
            $query->where('role', $this->selectedRole);
        }

        $users = $query->latest()->paginate(10);

        return view('livewire.manage-user', [
            'users' => $users
        ]);
    }
}
