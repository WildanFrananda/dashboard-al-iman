<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
     x-data="academicCalendar()">

    {{-- ── HEADER ─────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
        <div class="flex items-center gap-1">
            {{-- Prev month --}}
            <button wire:click="previousMonth"
                    wire:loading.attr="disabled"
                    wire:target="previousMonth,nextMonth"
                    class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors disabled:opacity-40">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <h3 class="font-bold text-sm text-gray-900 min-w-[130px] text-center select-none">
                {{ $this->monthLabel }}
            </h3>

            {{-- Next month --}}
            <button wire:click="nextMonth"
                    wire:loading.attr="disabled"
                    wire:target="previousMonth,nextMonth"
                    class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors disabled:opacity-40">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Admin: Tambah Acara --}}
        @if(auth()->user()?->role === 'admin')
            <button wire:click="openAddForm"
                    class="flex items-center gap-1 text-xs font-semibold text-white bg-[#0F609B] hover:bg-[#0a4d7c] px-3 py-1.5 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah
            </button>
        @endif
    </div>

    {{-- ── CALENDAR GRID ───────────────────────────────────────────────────── --}}
    <div wire:loading.class="opacity-40 pointer-events-none"
         wire:target="previousMonth,nextMonth"
         class="px-3 pt-2 pb-3 transition-opacity duration-200">

        {{-- Day-of-week headers --}}
        <div class="grid grid-cols-7 mb-1">
            @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $dow)
                <div class="text-center text-[10px] font-bold text-gray-400 py-1 uppercase tracking-wide">
                    {{ $dow }}
                </div>
            @endforeach
        </div>

        {{-- Calendar weeks --}}
        <div class="space-y-0.5">
            @foreach($this->calendarWeeks as $week)
                <div class="grid grid-cols-7 gap-0.5">
                    @foreach($week as $cell)
                        <div
                            @if($cell)
                                @click="selectDay({{ Js::from($cell) }})"
                                class="relative p-1 min-h-[46px] rounded-lg cursor-pointer transition-colors duration-100
                                       {{ $cell['hasLibur'] ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}"
                            @else
                                class="min-h-[46px]"
                            @endif
                        >
                            @if($cell)
                                {{-- Day number --}}
                                <div class="flex justify-center mb-0.5">
                                    <span class="text-[11px] font-semibold w-5 h-5 flex items-center justify-center rounded-full select-none
                                        @if($cell['isToday']) bg-[#0F609B] text-white
                                        @elseif($cell['hasLibur']) text-red-600 font-bold
                                        @elseif($cell['isSunday']) text-red-400
                                        @else text-gray-700
                                        @endif">
                                        {{ $cell['day'] }}
                                    </span>
                                </div>

                                {{-- Event pills (max 2 visible) --}}
                                @foreach(array_slice($cell['events'], 0, 2) as $ev)
                                    <div class="text-[8px] leading-tight truncate px-1 py-px rounded mb-px font-medium"
                                         style="background-color: {{ $ev['color'] }}22; color: {{ $ev['color'] }}">
                                        {{ $ev['title'] }}
                                    </div>
                                @endforeach

                                @if(count($cell['events']) > 2)
                                    <div class="text-[8px] text-gray-400 text-center leading-none">
                                        +{{ count($cell['events']) - 2 }} lagi
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── UPCOMING EVENTS LIST ────────────────────────────────────────────── --}}
    <div class="border-t border-gray-100 px-4 py-3">
        <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">
            Acara Mendatang
        </h4>

        <div class="space-y-2.5">
            @forelse($this->upcomingEvents as $event)
                <div class="flex items-start gap-3 group">
                    {{-- Date chip --}}
                    <div class="text-center rounded-lg px-2 py-1.5 min-w-[38px] shrink-0 leading-tight"
                         style="background-color: {{ $event->color }}18; color: {{ $event->color }}">
                        <div class="text-sm font-black">{{ $event->start_date->format('d') }}</div>
                        <div class="text-[9px] font-bold uppercase">
                            {{ ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][$event->start_date->month] }}
                        </div>
                    </div>

                    {{-- Event info --}}
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-xs font-semibold text-gray-800 leading-tight truncate">
                            {{ $event->title }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[9px] font-semibold px-1.5 py-px rounded-full capitalize"
                                  style="background-color: {{ $event->color }}18; color: {{ $event->color }}">
                                {{ $event->category }}
                            </span>
                            @if($event->location)
                                <span class="text-[9px] text-gray-400 truncate">{{ $event->location }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-gray-400 italic">Tidak ada acara mendatang.</p>
            @endforelse
        </div>
    </div>

    {{-- Flash notification --}}
    @if(session('cal_message'))
        <div class="mx-4 mb-3 p-2.5 bg-green-50 border border-green-200 rounded-lg text-xs text-green-700 font-medium flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('cal_message') }}
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════════════
         DETAIL MODAL — Alpine.js driven (no Livewire round-trip)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showDetail"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="closeDetail()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
         style="display:none">

        <div x-show="showDetail"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-auto overflow-hidden">

            {{-- Modal header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Acara pada</p>
                    <h4 class="font-bold text-gray-900 text-sm" x-text="selectedDay?.dayLabel"></h4>
                </div>
                <button @click="closeDetail()"
                        class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Event list --}}
            <div class="px-5 py-4 space-y-3 max-h-80 overflow-y-auto">
                <template x-if="selectedDay?.events?.length === 0">
                    <p class="text-sm text-gray-400 text-center py-4 italic">Tidak ada acara pada hari ini.</p>
                </template>

                <template x-for="ev in selectedDay?.events ?? []" :key="ev.id">
                    <div class="rounded-xl border border-gray-100 overflow-hidden">
                        {{-- Event color bar + title --}}
                        <div class="flex items-center gap-3 px-3 py-2.5"
                             :style="`background-color: ${ev.color}12`">
                            <div class="w-1 h-8 rounded-full shrink-0" :style="`background-color: ${ev.color}`"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 leading-tight" x-text="ev.title"></p>
                                <span class="text-[10px] font-semibold px-1.5 py-px rounded-full capitalize mt-0.5 inline-block"
                                      :style="`background-color: ${ev.color}22; color: ${ev.color}`"
                                      x-text="ev.category"></span>
                            </div>
                        </div>

                        {{-- Event details --}}
                        <div class="px-3 py-2 space-y-1 text-xs text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="ev.end_date ? ev.start_date + ' — ' + ev.end_date : ev.start_date"></span>
                            </div>
                            <template x-if="ev.location">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span x-text="ev.location"></span>
                                </div>
                            </template>
                            <template x-if="ev.description">
                                <p class="text-gray-500 leading-relaxed pt-0.5" x-text="ev.description"></p>
                            </template>
                        </div>

                        {{-- Admin controls --}}
                        @if(auth()->user()?->role === 'admin')
                            <div class="flex gap-2 px-3 py-2 border-t border-gray-100 bg-gray-50/50">
                                <button @click="editEvent(ev.id)"
                                        class="flex-1 text-xs font-semibold text-[#0F609B] hover:bg-blue-50 py-1.5 rounded-lg transition-colors text-center">
                                    Edit
                                </button>
                                <button @click="deleteEvent(ev.id)"
                                        class="flex-1 text-xs font-semibold text-red-600 hover:bg-red-50 py-1.5 rounded-lg transition-colors text-center">
                                    Hapus
                                </button>
                            </div>
                        @endif
                    </div>
                </template>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════
         FORM MODAL — Admin only, Livewire-driven
    ══════════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()?->role === 'admin')
        <div x-show="$wire.showFormModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="$wire.closeFormModal()"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
             style="display:none">

            <div x-show="$wire.showFormModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-auto overflow-hidden">

                {{-- Form header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h4 class="font-bold text-gray-900">
                        {{ $editingId ? 'Edit Acara' : 'Tambah Acara Baru' }}
                    </h4>
                    <button wire:click="closeFormModal"
                            class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Form body --}}
                <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Judul Acara <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="form_title" maxlength="255"
                               placeholder="contoh: Ujian Tengah Semester"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] transition-colors placeholder-gray-300">
                        @error('form_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category + Color row --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="form_category"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] transition-colors">
                                <option value="umum">Umum</option>
                                <option value="libur">Libur</option>
                                <option value="ujian">Ujian</option>
                                <option value="kegiatan">Kegiatan</option>
                            </select>
                            @error('form_category')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Warna
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="form_color"
                                       class="h-9 w-14 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                                <span class="text-xs text-gray-400 font-mono">{{ $form_color }}</span>
                            </div>
                            @error('form_color')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Date range --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="form_start_date"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] transition-colors">
                            @error('form_start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Tanggal Selesai
                                <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <input type="date" wire:model="form_end_date"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] transition-colors">
                            @error('form_end_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Location --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Lokasi
                            <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <input type="text" wire:model="form_location" maxlength="255"
                               placeholder="contoh: Aula Sekolah"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] transition-colors placeholder-gray-300">
                        @error('form_location')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Deskripsi
                            <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <textarea wire:model="form_description" rows="3" maxlength="1000"
                                  placeholder="Informasi tambahan tentang acara ini..."
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0F609B]/30 focus:border-[#0F609B] transition-colors resize-none placeholder-gray-300"></textarea>
                        @error('form_description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Form footer --}}
                <div class="flex gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    <button wire:click="closeFormModal"
                            class="flex-1 text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 py-2.5 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button wire:click="saveEvent"
                            wire:loading.attr="disabled"
                            wire:target="saveEvent"
                            class="flex-1 text-sm font-semibold text-white bg-[#0F609B] hover:bg-[#0a4d7c] disabled:opacity-60 py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="saveEvent">
                            {{ $editingId ? 'Simpan Perubahan' : 'Tambah Acara' }}
                        </span>
                        <span wire:loading wire:target="saveEvent" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

@script
<script>
Alpine.data('academicCalendar', () => ({
    showDetail: false,
    selectedDay: null,

    selectDay(cell) {
        this.selectedDay = cell;
        this.showDetail  = true;
    },

    closeDetail() {
        this.showDetail  = false;
        this.selectedDay = null;
    },

    editEvent(id) {
        this.closeDetail();
        this.$wire.openEditForm(id);
    },

    deleteEvent(id) {
        if (! confirm('Yakin ingin menghapus acara ini? Tindakan ini tidak dapat dibatalkan.')) return;
        this.closeDetail();
        this.$wire.deleteEvent(id);
    },
}));
</script>
@endscript
