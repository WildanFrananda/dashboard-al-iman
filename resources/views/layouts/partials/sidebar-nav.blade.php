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
    @php
        $nilaiRoute = match(auth()->user()->role ?? '') {
            'guru'  => route('manage-grade'),
            'murid' => route('student-grade'),
            'admin' => route('admin-grade-recap'),
            default => '#',
        };
        $nilaiActive = request()->routeIs('manage-grade')
            || request()->routeIs('student-grade')
            || request()->routeIs('admin-grade-recap');
    @endphp
    <!-- Penilaian Link -->
    <a href="{{ $nilaiRoute }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
       {{ $nilaiActive
    ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
    : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]'
       }}">
        <svg class="h-5 w-5 shrink-0 {{ $nilaiActive ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        Penilaian
    </a>
</ul>

<ul>
    @php
        $userRole = auth()->user()->role ?? '';
        $absensiRoute = in_array($userRole, ['admin', 'guru'])
            ? route('attendance-recap')
            : route('attendance');
        // Aktif HANYA pada route tujuan masing-masing role —
        // teacher-attendance tidak boleh ikut highlight menu ini.
        $absensiActive = match($userRole) {
            'guru', 'admin' => request()->routeIs('attendance-recap'),
            'murid'         => request()->routeIs('attendance'),
            default         => false,
        };
    @endphp
    <!-- Absensi Link -->
    <a href="{{ $absensiRoute }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
       {{ $absensiActive
    ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
    : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]'
       }}">
        <svg class="h-5 w-5 shrink-0 {{ $absensiActive ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>
        Absensi
    </a>
</ul>

@if(auth()->check() && auth()->user()->role === 'guru')
<ul>
    <!-- Input Absensi (Guru only) -->
    <a href="{{ route('teacher-attendance') }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
       {{ request()->routeIs('teacher-attendance')
    ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
    : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]'
       }}">
        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('teacher-attendance') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
        </svg>
        Input Absensi
    </a>
</ul>
@endif

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
        <!-- Manage Student Link -->
        <a href="{{ route('manage-student') }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
           {{ request()->routeIs('manage-student')
            ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
            : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]' 
           }}">
            <!-- Icon Students -->
            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('manage-student') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4.26 10.147L12 14.64l7.74-4.493a3 3 0 011.13 1.897V19.5a2.25 2.25 0 01-2.25 2.25h-13.5A2.25 2.25 0 013 19.5v-7.46a3 3 0 011.26-1.893z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 4.5l9 5.25-9 5.25-9-5.25 9-5.25z" />
            </svg>
            Manage Murid
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

    <ul>
        <!-- Manage Settings Link -->
        <a href="{{ route('manage-settings') }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
           {{ request()->routeIs('manage-settings')
            ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
            : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]'
           }}">
            <!-- Icon Settings / Gear -->
            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('manage-settings') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Pengaturan Sekolah
        </a>
    </ul>

    <ul>
        <!-- Manage Schedule Link -->
        <a href="{{ route('manage-schedule') }}" class="group flex gap-x-4 rounded-lg px-4 py-3 text-sm font-semibold leading-6 transition-all duration-200 hover:translate-x-1
           {{ request()->routeIs('manage-schedule')
            ? 'bg-[#0F609B] text-white shadow-md shadow-blue-900/10'
            : 'text-gray-600 hover:bg-gray-50 hover:text-[#0F609B]' 
           }}">
            <!-- Icon Calendar / Schedule -->
            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('manage-schedule') ? 'text-white' : 'text-gray-400 group-hover:text-[#0F609B]' }}"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
            </svg>
            Manage Jadwal
        </a>
    </ul>
@endif