<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tambah Pelajaran - SIAKMAN')]
class CreateSubject extends Component {
    public $subject_code = '';

    public $subject_name = '';

    public $category = 'Wajib';

    public $is_active = true;

    protected $rules = [
        'subject_code' => 'required|string|unique:subjects,subject_code|max:50',
        'subject_name' => 'required|string|max:255',
        'category' => 'required|in:Wajib,Muatan Lokal,Ekstrakurikuler',
        'is_active' => 'boolean',
    ];

    public function mount() {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
    }

    public function save() {
        $this->validate();

        Subject::create([
            'subject_code' => $this->subject_code,
            'subject_name' => $this->subject_name,
            'category' => $this->category,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Mata pelajaran berhasil ditambahkan.');

        return redirect()->route('manage-subject');
    }

    public function render() {
        return view('livewire.create-subject');
    }
}
