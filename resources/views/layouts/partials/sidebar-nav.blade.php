<ul>
    <!-- Dashboard Link -->
    <a href="{{ route('dashboard') }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
       {{ request()->routeIs('dashboard')
    ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
    : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]' 
       }}">
        <!-- Icon Dashboard -->
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
        </svg>
        Dashboard
    </a>
</ul>

<ul>
    <!-- Nilai Link -->
    <a href="#"
        class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 text-gray-600 hover:bg-gray-50 hover:text-[#0F609B] transition-all duration-200 hover:translate-x-1">
        <!-- Icon Nilai (Edit/Note) -->
        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-[#0F609B]" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
        </svg>
        Nilai
    </a>
</ul>

<ul>
    @php
        $absensiRoute = auth()->check() && auth()->user()->role === 'guru' ? route('teacher-attendance') : route('attendance');
    @endphp
    <!-- Absensi Link -->
    <a href="{{ $absensiRoute }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
       {{ request()->routeIs('attendance') || request()->routeIs('teacher-attendance')
    ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
    : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]' 
       }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('attendance') || request()->routeIs('teacher-attendance') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>
        Absensi
    </a>
</ul>

@if(auth()->check() && auth()->user()->role === 'admin')
    <ul>
        <!-- Manage User Link -->
        <a href="{{ route('manage-user') }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
           {{ request()->routeIs('manage-user')
            ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
            : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]' 
           }}">
            <!-- Icon User Group/Manage User -->
            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('manage-user') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
            Manage User
        </a>
    </ul>

    <ul>
        <!-- Manage Subject Link -->
        <a href="{{ route('manage-subject') }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
           {{ request()->routeIs('manage-subject')
            ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
            : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]' 
           }}">
            <!-- Icon Book / Subject -->
            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('manage-subject') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
            Manage Pelajaran
        </a>
    </ul>

    <ul>
        <!-- Manage Class Link -->
        <a href="{{ route('manage-class') }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
           {{ request()->routeIs('manage-class')
            ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
            : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]' 
           }}">
            <!-- Icon Building / Class -->
            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('manage-class') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.75M5.25 9h3.75m-3.75 3h3.75m-3.75 3h3.75m3-8.25h3.75m-3.75 3h3.75m-3.75 3h3.75M9 21v-8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 21h18M12 21h6" />
            </svg>
            Manage Kelas
        </a>
    </ul>
@endif