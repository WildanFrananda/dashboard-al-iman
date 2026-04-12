<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Kelas;
use App\Models\ProfilGuru;
use App\Models\ProfilMurid;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

    // Promotion State
    public $showPromotionModal = false;

    public $promotionStep = 1;

    public $selectedStayBackIds = []; // IDs of ProfilMurid who will NOT be promoted

    public $newAcademicYear = '';

    public function mount() {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        $this->tahun_ajaran = date('Y').'/'.(date('Y') + 1);

        $currentYear = (int) date('Y');
        $this->newAcademicYear = ($currentYear + 1).'/'.($currentYear + 2);
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
            'kode_kelas' => [
                'required', 'string', 'max:20',
                $this->editingId
                    ? Rule::unique('kelas', 'kode_kelas')->ignore($this->editingId)
                    : Rule::unique('kelas', 'kode_kelas'),
            ],
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

    // PROMOTION METHODS
    public function openPromotion() {
        $this->promotionStep = 1;
        $this->selectedStayBackIds = [];
        $this->showPromotionModal = true;
    }

    public function startPromotion() {
        // Step 1 -> 2: Show list of students to select who stays back
        $this->promotionStep = 2;
    }

    public function processPromotion() {
        $students = ProfilMurid::where('status', 'aktif')->get();

        DB::beginTransaction();
        try {
            foreach ($students as $student) {
                // 1. Ambil Kelas saat ini (di tahun ajaran sumber)
                $currentClass = $student->kelas()->wherePivot('tahun_ajaran', $this->tahun_ajaran)->first();
                if (!$currentClass) {
                    continue;
                }

                // 2. Berpindah (Naik atau Tetap)?
                $shouldStayBack = in_array((string) $student->id, $this->selectedStayBackIds);

                if ($shouldStayBack) {
                    // Cari/Buat header kelas yang SAMA untuk TAHUN BARU
                    $nextClass = Kelas::firstOrCreate(
                        ['level' => $currentClass->level, 'kelompok' => $currentClass->kelompok, 'tahun_ajaran' => $this->newAcademicYear],
                        [
                            'nama_kelas' => $currentClass->nama_kelas,
                            'kode_kelas' => $currentClass->kode_kelas.'-'.$this->newAcademicYear,
                            'wali_kelas_id' => $currentClass->wali_kelas_id,
                        ]
                    );
                } else {
                    // Cek Kelulusan (Level 6)
                    if ($currentClass->level >= 6) {
                        $student->update(['status' => 'lulus']);

                        continue;
                    }

                    // Cari/Buat header kelas LEVEL BERIKUTNYA untuk TAHUN BARU
                    $nextLevel = $currentClass->level + 1;
                    $nextClass = Kelas::where('level', $nextLevel)
                        ->where('kelompok', $currentClass->kelompok)
                        ->where('tahun_ajaran', $this->newAcademicYear)
                        ->first();

                    if (!$nextClass) {
                        // Cari template untuk nama/wali kelas dari record lama
                        $template = Kelas::where('level', $nextLevel)->where('kelompok', $currentClass->kelompok)->first();
                        $nextClass = Kelas::create([
                            'level' => $nextLevel,
                            'kelompok' => $currentClass->kelompok,
                            'tahun_ajaran' => $this->newAcademicYear,
                            'nama_kelas' => $template ? $template->nama_kelas : "Kelas {$nextLevel}{$currentClass->kelompok}",
                            'kode_kelas' => "SD-{$nextLevel}{$currentClass->kelompok}-{$this->newAcademicYear}",
                            'wali_kelas_id' => $template ? $template->wali_kelas_id : null,
                        ]);
                    }
                }

                // Attach ke kelas tujuan di tahun ajaran baru
                // Menggunakan syncWithoutDetaching agar id murid & id kelas & tahun_ajaran unik di pivot
                $student->kelas()->syncWithoutDetaching([
                    $nextClass->id => ['tahun_ajaran' => $this->newAcademicYear],
                ]);
            }

            DB::commit();
            session()->flash('message', 'Proses kenaikan kelas berhasil untuk tahun ajaran '.$this->newAcademicYear);
            $this->showPromotionModal = false;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses kenaikan: '.$e->getMessage());
        }
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
        $activeStudents = ProfilMurid::where('status', 'aktif')->orderBy('nama_lengkap')->get();

        return view('livewire.manage-class', [
            'classes' => $classes,
            'gurus' => $gurus,
            'activeStudents' => $activeStudents,
        ]);
    }
}
