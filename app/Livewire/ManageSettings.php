<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pengaturan Sekolah - SIAKMAN')]
class ManageSettings extends Component {
    public string $jumlah_ekskul = '';

    public string $tingkat_akreditasi = '';

    public function mount(): void {
        $this->authorizeAdmin();

        $this->jumlah_ekskul = SchoolSetting::get('jumlah_ekskul', '0');
        $this->tingkat_akreditasi = SchoolSetting::get('tingkat_akreditasi', '-');
    }

    public function save(): void {
        $this->authorizeAdmin();

        $this->validate([
            'jumlah_ekskul' => ['required', 'integer', 'min:0', 'max:999'],
            'tingkat_akreditasi' => ['required', 'string', 'max:10'],
        ]);

        SchoolSetting::set('jumlah_ekskul', $this->jumlah_ekskul);
        SchoolSetting::set('tingkat_akreditasi', $this->tingkat_akreditasi);

        session()->flash('settings_message', 'Pengaturan berhasil disimpan.');
    }

    private function authorizeAdmin(): void {
        if (Auth::user()?->role !== 'admin') {
            redirect()->route('dashboard');

            return;
        }
    }

    public function render() {
        return view('livewire.manage-settings');
    }
}
