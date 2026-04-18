<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Subject;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage Pelajaran - SIAKMAN')]
class ManageSubject extends Component {
    use WithPagination;

    public string $search = '';

    // Modal state
    public bool $showModal = false;

    public ?int $editingId = null;

    // Form fields
    public string $form_code = '';

    public string $form_name = '';

    public string $form_category = 'Wajib';

    public bool $form_active = true;

    public function mount(): void {
        if (auth()->user()?->role !== 'admin') {
            redirect()->route('dashboard');
        }
    }

    public function updatedSearch(): void {
        $this->resetPage();
    }

    // ── Modal open/close ──────────────────────────────────────────────────────

    public function openAddForm(): void {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditForm(int $id): void {
        $subject = Subject::findOrFail($id);

        $this->editingId = $id;
        $this->form_code = $subject->subject_code;
        $this->form_name = $subject->subject_name;
        $this->form_category = $subject->category;
        $this->form_active = (bool) $subject->is_active;
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function closeModal(): void {
        $this->showModal = false;
        $this->resetForm();
    }

    // ── CRUD ─────────────────────────────────────────────────────────────────

    public function save(): void {
        $this->validate([
            'form_code' => [
                'required', 'string', 'max:50',
                $this->editingId
                    ? Rule::unique('subjects', 'subject_code')->ignore($this->editingId)
                    : Rule::unique('subjects', 'subject_code'),
            ],
            'form_name' => 'required|string|max:255',
            'form_category' => 'required|in:Wajib,Muatan Lokal,Ekstrakurikuler',
            'form_active' => 'boolean',
        ]);

        $data = [
            'subject_code' => $this->form_code,
            'subject_name' => $this->form_name,
            'category' => $this->form_category,
            'is_active' => $this->form_active,
        ];

        if ($this->editingId) {
            Subject::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Mata pelajaran berhasil diperbarui.');
        } else {
            Subject::create($data);
            session()->flash('message', 'Mata pelajaran berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete(int $id): void {
        Subject::findOrFail($id)->delete();
        session()->flash('message', 'Mata pelajaran berhasil dihapus.');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render() {
        $subjects = Subject::query()
            ->when($this->search !== '', fn ($q) => $q->where('subject_name', 'like', '%'.$this->search.'%')
                ->orWhere('subject_code', 'like', '%'.$this->search.'%')
            )
            ->latest()
            ->paginate(10);

        return view('livewire.manage-subject', compact('subjects'));
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function resetForm(): void {
        $this->form_code = '';
        $this->form_name = '';
        $this->form_category = 'Wajib';
        $this->form_active = true;
        $this->resetErrorBag();
    }
}
