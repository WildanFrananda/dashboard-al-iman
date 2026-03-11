<div class="flex flex-col gap-6 h-full">
    
    <!-- TOP FILTER BAR -->
    <div class="bg-[#0F609B] rounded-xl shadow-sm px-6 py-4 flex flex-col md:flex-row items-center gap-4 shrink-0">
        
        <!-- Teacher Name Input Group -->
        <div class="w-full md:flex-1 flex items-center gap-3">
            <div class="text-white shrink-0">
                <!-- ID/User Icon -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 011.847 1.256l1.24 3.102A2 2 0 0017.69 9H21a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" class="hidden"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <input type="text" wire:model="teacherName" placeholder="Masukan nama pengajar disini.." 
                   class="w-full bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-white">
        </div>

        <!-- Class Select Group -->
        <div class="w-full md:w-auto flex items-center gap-3">
            <div class="text-white shrink-0">
                <!-- Door Icon -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19v-2M4 17V5a2 2 0 012-2h12a2 2 0 012 2v12M4 17h16M14 10v.01" />
                </svg>
            </div>
            <select wire:model="classId" class="w-full md:w-[130px] bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-white">
                <option value="">Kelas..</option>
                <option value="1">Kelas 1</option>
                <option value="2">Kelas 2</option>
            </select>
        </div>

        <!-- Date Input Group -->
        <div class="w-full md:w-auto flex items-center gap-3">
            <div class="text-white shrink-0">
                <!-- Calendar Icon -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="relative w-full md:w-[130px]">
                <input type="text" wire:model="date" placeholder="dd/mm/yy" 
                       class="w-full bg-white border-0 rounded-md px-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-white">
            </div>
        </div>

        <!-- Submit Button -->
        <button wire:click="submit" class="w-full md:w-auto bg-[#F28B2B] hover:bg-[#D97706] text-white font-medium py-2.5 px-8 rounded-md transition-colors shrink-0">
            Submit
        </button>
    </div>

    <!-- MAIN CONTENT AREA (TABLE) -->
    <div class="bg-white rounded-[20px] shadow-sm overflow-hidden flex-1 border border-gray-100 flex flex-col">
        
        <!-- Table Header -->
        <div class="bg-[#F1F5F9] px-6 py-4 flex justify-between items-center shrink-0">
            <div class="font-bold text-gray-800 text-sm">Nama</div>
            <div class="font-bold text-gray-800 text-sm w-[350px] pl-2 md:pl-0">Status Kehadiran</div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-dashed divide-gray-200 border-t border-gray-200 flex-1 overflow-y-auto">
            @foreach($students as $index => $student)
            <div class="px-6 py-4 flex flex-col md:flex-row md:justify-between md:items-center hover:bg-gray-50/50 transition-colors gap-3 md:gap-0">
                
                <!-- Student Name -->
                <div class="font-normal text-gray-800 text-sm">
                    {{ $student['name'] }}
                </div>

                <!-- Radio Buttons Container -->
                <div class="flex items-center gap-4 md:gap-6 w-full md:w-[350px] flex-wrap md:flex-nowrap">
                    <!-- Hadir -->
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" wire:model="students.{{ $index }}.status" value="Hadir" 
                               name="status_{{ $index }}"
                               class="w-[18px] h-[18px] text-gray-800 border-2 border-gray-500 focus:ring-gray-800 bg-white cursor-pointer">
                        <span class="text-sm font-normal text-gray-800">Hadir</span>
                    </label>

                    <!-- Izin -->
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" wire:model="students.{{ $index }}.status" value="Izin" 
                               name="status_{{ $index }}"
                               class="w-[18px] h-[18px] text-gray-800 border-2 border-gray-500 focus:ring-gray-800 bg-white cursor-pointer">
                        <span class="text-sm font-normal text-gray-800">Izin</span>
                    </label>

                    <!-- Sakit -->
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" wire:model="students.{{ $index }}.status" value="Sakit" 
                               name="status_{{ $index }}"
                               class="w-[18px] h-[18px] text-gray-800 border-2 border-gray-500 focus:ring-gray-800 bg-white cursor-pointer">
                        <span class="text-sm font-normal text-gray-800">Sakit</span>
                    </label>

                    <!-- Alfa -->
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" wire:model="students.{{ $index }}.status" value="Alfa" 
                               name="status_{{ $index }}"
                               class="w-[18px] h-[18px] text-gray-800 border-2 border-gray-500 focus:ring-gray-800 bg-white cursor-pointer">
                        <span class="text-sm font-normal text-gray-800">Alfa</span>
                    </label>
                </div>

            </div>
            @endforeach
        </div>
        
    </div>

</div>
