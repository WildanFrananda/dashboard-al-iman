<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\AcademicEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Embedded kalender akademik (bukan full-page, tidak pakai #[Layout]).
 * Di-render di dalam dashboard.blade.php via @livewire('academic-calendar').
 */
class AcademicCalendar extends Component {
    public int $year;
    public int $month;

    // --- Admin form state ---
    public bool  $showFormModal = false;
    public ?int  $editingId     = null;

    public string $form_title       = '';
    public string $form_category    = 'umum';
    public string $form_start_date  = '';
    public string $form_end_date    = '';
    public string $form_location    = '';
    public string $form_description = '';
    public string $form_color       = '#0F609B';

    public function mount(): void {
        $this->year  = now()->year;
        $this->month = now()->month;
    }

    // ── Month navigation ──────────────────────────────────────────────────────

    public function previousMonth(): void {
        if ($this->month === 1) {
            $this->month = 12;
            $this->year--;
        } else {
            $this->month--;
        }
    }

    public function nextMonth(): void {
        if ($this->month === 12) {
            $this->month = 1;
            $this->year++;
        } else {
            $this->month++;
        }
    }

    // ── Calendar grid computation ─────────────────────────────────────────────

    #[Computed]
    public function calendarWeeks(): array {
        $firstOfMonth = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $lastOfMonth  = $firstOfMonth->copy()->endOfMonth()->startOfDay();
        $daysInMonth  = $firstOfMonth->daysInMonth;

        // Monday-first offset (Mon=0 … Sun=6)
        // Carbon: Sun=0, Mon=1 … Sat=6  →  (dow + 6) % 7
        $startOffset = ($firstOfMonth->dayOfWeek + 6) % 7;

        // Fetch all events that overlap this month (single query)
        $events = AcademicEvent::where('start_date', '<=', $lastOfMonth)
            ->where(function ($q) use ($firstOfMonth) {
                $q->where('end_date', '>=', $firstOfMonth)
                  ->orWhere(function ($q2) use ($firstOfMonth) {
                      $q2->whereNull('end_date')
                         ->where('start_date', '>=', $firstOfMonth);
                  });
            })
            ->orderBy('start_date')
            ->get();

        $today = now()->startOfDay();
        $weeks = [];
        $week  = array_fill(0, $startOffset, null); // blank cells before day 1

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date    = Carbon::create($this->year, $this->month, $day)->startOfDay();
            $dateStr = $date->format('Y-m-d');

            // Events that overlap this specific day
            $dayEvents = $events->filter(function ($event) use ($date) {
                $start = Carbon::parse($event->start_date)->startOfDay();
                $end   = $event->end_date
                    ? Carbon::parse($event->end_date)->startOfDay()
                    : $start;

                return $date->between($start, $end);
            })->values();

            $week[] = [
                'day'      => $day,
                'date'     => $dateStr,
                'dayLabel' => $day . ' ' . $this->monthName($this->month) . ' ' . $this->year,
                'isToday'  => $date->eq($today),
                'isSunday' => $date->dayOfWeek === 0,
                'hasLibur' => $dayEvents->contains('category', 'libur'),
                'events'   => $dayEvents->map(fn ($e) => [
                    'id'          => $e->id,
                    'title'       => $e->title,
                    'category'    => $e->category,
                    'color'       => $e->color,
                    'start_date'  => $e->start_date->format('d M Y'),
                    'end_date'    => $e->end_date?->format('d M Y'),
                    'location'    => $e->location,
                    'description' => $e->description,
                ])->toArray(),
            ];

            if (count($week) === 7) {
                $weeks[] = $week;
                $week    = [];
            }
        }

        // Pad last week with nulls
        if (! empty($week)) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    #[Computed]
    public function upcomingEvents() {
        return AcademicEvent::upcoming()->limit(3)->get();
    }

    #[Computed]
    public function monthLabel(): string {
        return $this->monthName($this->month) . ' ' . $this->year;
    }

    // ── Admin CRUD ────────────────────────────────────────────────────────────

    public function openAddForm(): void {
        $this->authorizeAdmin();
        $this->resetForm();
        $this->editingId     = null;
        $this->showFormModal = true;
    }

    public function openEditForm(int $id): void {
        $this->authorizeAdmin();
        $event = AcademicEvent::findOrFail($id);

        $this->editingId          = $id;
        $this->form_title         = $event->title;
        $this->form_category      = $event->category;
        $this->form_start_date    = $event->start_date->format('Y-m-d');
        $this->form_end_date      = $event->end_date?->format('Y-m-d') ?? '';
        $this->form_location      = $event->location ?? '';
        $this->form_description   = $event->description ?? '';
        $this->form_color         = $event->color;
        $this->showFormModal      = true;
    }

    /** Auto-update warna saat kategori berubah di form. */
    public function updatedFormCategory(): void {
        $this->form_color = AcademicEvent::defaultColor($this->form_category);
    }

    public function saveEvent(): void {
        $this->authorizeAdmin();

        $this->validate([
            'form_title'       => 'required|string|max:255',
            'form_category'    => 'required|in:libur,ujian,kegiatan,umum',
            'form_start_date'  => 'required|date',
            'form_end_date'    => 'nullable|date|after_or_equal:form_start_date',
            'form_location'    => 'nullable|string|max:255',
            'form_description' => 'nullable|string|max:1000',
            'form_color'       => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $data = [
            'title'       => $this->form_title,
            'category'    => $this->form_category,
            'start_date'  => $this->form_start_date,
            'end_date'    => $this->form_end_date ?: null,
            'location'    => $this->form_location ?: null,
            'description' => $this->form_description ?: null,
            'color'       => $this->form_color,
        ];

        if ($this->editingId) {
            AcademicEvent::findOrFail($this->editingId)->update($data);
            session()->flash('cal_message', 'Acara berhasil diperbarui.');
        } else {
            AcademicEvent::create($data);
            session()->flash('cal_message', 'Acara berhasil ditambahkan.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function deleteEvent(int $id): void {
        $this->authorizeAdmin();
        AcademicEvent::findOrFail($id)->delete();
        session()->flash('cal_message', 'Acara berhasil dihapus.');
    }

    public function closeFormModal(): void {
        $this->showFormModal = false;
        $this->resetErrorBag();
        $this->resetForm();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function authorizeAdmin(): void {
        if (Auth::user()?->role !== 'admin') {
            abort(403);
        }
    }

    private function resetForm(): void {
        $this->form_title       = '';
        $this->form_category    = 'umum';
        $this->form_start_date  = '';
        $this->form_end_date    = '';
        $this->form_location    = '';
        $this->form_description = '';
        $this->form_color       = AcademicEvent::defaultColor('umum');
        $this->editingId        = null;
    }

    private function monthName(int $month): string {
        return [
            1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
            4  => 'April',     5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',   11 => 'November',  12 => 'Desember',
        ][$month];
    }

    public function render() {
        return view('livewire.academic-calendar');
    }
}
