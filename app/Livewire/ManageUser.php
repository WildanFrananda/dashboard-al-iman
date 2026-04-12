<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage User - SIAKMAN')]
class ManageUser extends Component {
    use WithPagination;

    public string $search       = '';
    public string $selectedRole = 'all';

    // --- Modal state ---
    public bool  $showModal  = false;
    public ?int  $editingId  = null;

    // --- Form fields ---
    public string $form_name     = '';
    public string $form_email    = '';
    public string $form_role     = 'murid';
    public string $form_password = '';

    public function mount(): void {
        if (Auth::user()?->role !== 'admin') {
            redirect()->route('dashboard');
        }
    }

    public function updatedSearch(): void {
        $this->resetPage();
    }

    public function updatedSelectedRole(): void {
        $this->resetPage();
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    public function openAddForm(): void {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditForm(int $id): void {
        $user = User::findOrFail($id);

        $this->editingId   = $id;
        $this->form_name   = $user->name;
        $this->form_email  = $user->email;
        $this->form_role   = $user->role;
        $this->form_password = '';
        $this->showModal   = true;
    }

    public function saveUser(): void {
        $rules = [
            'form_name'  => ['required', 'string', 'max:255'],
            'form_email' => ['required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->editingId),
            ],
            'form_role'  => ['required', 'in:admin,guru,murid'],
        ];

        if (! $this->editingId) {
            $rules['form_password'] = ['required', 'string', 'min:8'];
        } else {
            $rules['form_password'] = ['nullable', 'string', 'min:8'];
        }

        $this->validate($rules);

        $data = [
            'name'  => $this->form_name,
            'email' => $this->form_email,
            'role'  => $this->form_role,
        ];

        if ($this->form_password !== '') {
            $data['password'] = Hash::make($this->form_password);
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            session()->flash('user_message', 'User berhasil diperbarui.');
        } else {
            $data['password'] ??= Hash::make($this->form_password);
            User::create($data);
            session()->flash('user_message', 'User berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function deleteUser(int $id): void {
        if ($id === Auth::id()) {
            session()->flash('user_error', 'Tidak dapat menghapus akun sendiri.');
            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('user_message', 'User berhasil dihapus.');
    }

    public function closeModal(): void {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->resetForm();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resetForm(): void {
        $this->form_name     = '';
        $this->form_email    = '';
        $this->form_role     = 'murid';
        $this->form_password = '';
        $this->editingId     = null;
    }

    public function render() {
        $query = User::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->selectedRole !== 'all') {
            $query->where('role', $this->selectedRole);
        }

        $users = $query->latest()->paginate(10);

        return view('livewire.manage-user', ['users' => $users]);
    }
}
