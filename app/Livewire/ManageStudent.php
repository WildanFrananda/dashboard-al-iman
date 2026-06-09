<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Kelas;
use App\Models\ProfilMurid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manage Murid - SIAKMAN')]
class ManageStudent extends Component {
    use WithPagination;

    public $search = '';

    public $selectedStatus = 'all';

    public $selectedYear = '';

    // Form fields
    public $name = '';

    public $email = '';

    public $password = '';

    public $nis = '';

    public $nama_lengkap = '';

    public $status = 'aktif';

    public $kelas_id = null;

    public $tahun_ajaran = '';

    // Daftarkan murid baru: pilih murid (profil_murid) yang belum di-assign ke kelas
    public $selectedMuridId = null;

    // Edit state
    public $editingId = null; // ProfilMurid ID

    public $showForm = false;

    public $confirmingDelete = null;

    protected $queryString = ['search', 'selectedStatus'];

    public function mount() {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        $this->tahun_ajaran = date('Y').'/'.(date('Y') + 1);
        $this->selectedYear = $this->tahun_ajaran;
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
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->nis = '';
        $this->nama_lengkap = '';
        $this->status = 'aktif';
        $this->kelas_id = null;
        $this->editingId = null;
        $this->selectedMuridId = null;
    }

    public function save() {
        $rules = [
            'nis' => ['required', 'string', Rule::unique('profil_murid', 'nis')->ignore($this->editingId ?? $this->selectedMuridId)],
            'status' => 'required|in:aktif,lulus,pindah,keluar',
        ];

        if ($this->editingId) {
            $userId = ProfilMurid::find($this->editingId)?->user_id;
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = ['required', 'email', Rule::unique('users', 'email')->ignore($userId)];
            $rules['nama_lengkap'] = 'required|string|max:255';
            $rules['password'] = 'nullable|min:6';
        } else {
            // Daftarkan murid: pilih murid yang sudah punya akun (dari Manage User) lalu assign ke kelas
            $rules['selectedMuridId'] = 'required|exists:profil_murid,id';
            $rules['kelas_id'] = 'required|exists:kelas,id';
        }

        $this->validate($rules, [
            'selectedMuridId.required' => 'Pilih murid yang akan didaftarkan.',
            'kelas_id.required' => 'Pilih kelas.',
        ]);

        DB::beginTransaction();
        try {
            if ($this->editingId) {
                $profil = ProfilMurid::findOrFail($this->editingId);
                $user = $profil->user;
                $user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                ]);
                if ($this->password) {
                    $user->update(['password' => Hash::make($this->password)]);
                }

                $profil->update([
                    'nis' => $this->nis,
                    'nama_lengkap' => $this->nama_lengkap,
                    'status' => $this->status,
                ]);

                // Update class if changed (manual assignment)
                if ($this->kelas_id) {
                    $profil->kelas()->syncWithoutDetaching([
                        $this->kelas_id => ['tahun_ajaran' => $this->tahun_ajaran],
                    ]);
                }
            } else {
                // Assign murid yang sudah ada (akunnya dibuat di Manage User)
                $profil = ProfilMurid::findOrFail($this->selectedMuridId);

                $profil->update([
                    'nis' => $this->nis,
                    'status' => $this->status,
                ]);

                $profil->kelas()->syncWithoutDetaching([
                    $this->kelas_id => ['tahun_ajaran' => $this->tahun_ajaran],
                ]);
            }

            DB::commit();
            session()->flash('message', $this->editingId ? 'Data murid berhasil diupdate.' : 'Murid berhasil didaftarkan.');
            $this->closeForm();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function delete(int $id): void {
        $profil = ProfilMurid::with('user')->findOrFail($id);
        $user = $profil->user;
        $profil->delete();
        if ($user) {
            $user->delete();
        }
        session()->flash('message', 'Data murid berhasil dihapus.');
    }

    public function edit($id) {
        $profil = ProfilMurid::with('user', 'kelas')->findOrFail($id);
        $this->editingId = $profil->id;
        $this->name = $profil->user->name;
        $this->email = $profil->user->email;
        $this->nis = $profil->nis;
        $this->nama_lengkap = $profil->nama_lengkap;
        $this->status = $profil->status;

        $currentKelas = $profil->kelas()->wherePivot('tahun_ajaran', $this->tahun_ajaran)->first();
        $this->kelas_id = $currentKelas ? $currentKelas->id : null;

        $this->showForm = true;
    }

    public function render() {
        $students = ProfilMurid::query()
            ->with(['user', 'kelas' => function ($q) {
                $q->wherePivot('tahun_ajaran', $this->selectedYear);
            }])
            ->when($this->search, function ($query) {
                $query->where('nama_lengkap', 'ilike', "%{$this->search}%")
                    ->orWhere('nis', 'ilike', "%{$this->search}%")
                    ->orWhereHas('user', function ($q) {
                        $q->where('email', 'ilike', "%{$this->search}%");
                    });
            })
            ->when($this->selectedStatus !== 'all', function ($query) {
                $query->where('status', $this->selectedStatus);
            })
            ->orderBy('nama_lengkap')
            ->paginate(10);

        // Murid yang belum di-assign ke kelas manapun pada tahun ajaran ini.
        // Catatan: di dalam whereDoesntHave, wherePivot() tidak berlaku — rujuk kolom pivot langsung.
        $unassignedMurids = ProfilMurid::with('user')
            ->whereDoesntHave('kelas', function ($q) {
                $q->where('kelas_murid.tahun_ajaran', $this->tahun_ajaran);
            })
            ->orderBy('nama_lengkap')
            ->get();

        $classes = Kelas::orderBy('nama_kelas')->get();
        $availableYears = DB::table('kelas_murid')->distinct()->pluck('tahun_ajaran')->toArray();
        // Ensure current year and potential next year are in the list
        $availableYears[] = $this->tahun_ajaran;
        $nextYear = ((int) substr($this->tahun_ajaran, 0, 4) + 1).'/'.((int) substr($this->tahun_ajaran, 5, 4) + 1);
        $availableYears[] = $nextYear;
        $availableYears = array_unique($availableYears);
        rsort($availableYears);

        return view('livewire.manage-student', [
            'students' => $students,
            'classes' => $classes,
            'availableYears' => $availableYears,
            'unassignedMurids' => $unassignedMurids,
        ]);
    }
}
