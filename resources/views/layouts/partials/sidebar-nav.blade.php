<ul>
    <!-- Dashboard Link -->
    <a href="{{ route('dashboard') }}" 
       class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200
       {{ request()->routeIs('dashboard') 
          ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10' 
          : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]' 
       }}">
        <!-- Icon Dashboard -->
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
        </svg>
        Dashboard
    </a>
</ul>

<ul>
    <!-- Nilai Link -->
    <a href="#" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 text-gray-600 hover:bg-gray-50 hover:text-[#0F609B] transition-colors">
        <!-- Icon Nilai (Edit/Note) -->
        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-[#0F609B]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
        </svg>
        Nilai
    </a>
</ul>

<ul>
    <!-- Absensi Link -->
    <a href="{{ route('attendance') }}" 
       class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200
       {{ request()->routeIs('attendance') 
          ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10' 
          : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]' 
       }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('attendance') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>
        Absensi
    </a>
</ul>

<ul>
    <!-- Kalender Link -->
    <a href="#" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 text-gray-600 hover:bg-gray-50 hover:text-[#0F609B] transition-colors">
        <!-- Icon Calendar -->
        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-[#0F609B]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
        </svg>
        Kalender Akademik
    </a>
</ul>