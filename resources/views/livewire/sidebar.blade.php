<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<div id="sidebar" class="bg-gray-800 text-white hidden lg:block shadow">
    <div class="flex flex-col">
        <div class="flex-1 flex flex-col justify-center">
            <div class="flex flex-col content-between">
                <div>
                    <div x-data="{ open: false }" @click="open = !open" class="relative transition duration-200 ease-in">
                        <x-nav-link class="text-lg sidebar-field w-full text-left text-white">
                            <p class="fa-solid fa-gear text-2xl ml-2 mr-2 text-white"></p>
                            <p class="sidebar-text">{{ __('Panel') }}</p>
                            <span class="ml-auto text-white" aria-hidden="true">
                                <svg class="w-4 h-4 transition-transform transform text-white" :class="{ 'rotate-180': open }"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </x-nav-link>

                        <div x-show="open" class="transition-transform duration-200 ease-in-out" @click="open = !open">
                            <x-nav-link class="text-lg sidebar-field sub-sidebar-field w-full text-left text-white" href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                                <p class="fa-solid fa-house text-2xl ml-2 mr-2 text-white"></p>
                                <p class="sidebar-text">{{ __('Home') }}</p>
                            </x-nav-link>
                            <x-nav-link class="text-lg sidebar-field sub-sidebar-field w-full text-left text-white border-b-gray-700" href="{{ route('courses') }}" :active="request()->routeIs('courses')">
                                <p class="fa-solid fa-graduation-cap text-2xl ml-2 mr-2 text-white"></p>
                                <p class="sidebar-text">{{ __('Cursos') }}</p>
                            </x-nav-link>
                        </div>
                    </div>
                    <div x-data="{ open: false }" @click="open = !open" class="relative transition duration-200 ease-in">
                        <x-nav-link class="text-lg sidebar-field w-full text-left text-white">
                            <p class="material-symbols-outlined text-3xl ml-2 mr-2 text-white">shield_person</p>
                            <p class="sidebar-text">{{ __('Administración') }}</p>
                            <span class="ml-auto text-white" aria-hidden="true">
                                <svg class="w-4 h-4 transition-transform transform text-white" :class="{ 'rotate-180': open }"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </x-nav-link>

                        <div x-show="open" class="transition duration-200 ease-in-out" @click="open = !open">
                            <x-nav-link class="text-lg sidebar-field w-full text-left text-white" href="{{ route('admin-attendance') }}" :active="request()->routeIs('admin-attendance')">
                                <p class="fa-regular fa-calendar-check text-2xl ml-2 mr-2 text-white"></p>
                                <p class="sidebar-text">{{ __('Asistencia') }}</p>
                            </x-nav-link>
                            <x-nav-link class="text-lg sidebar-field w-full text-left text-white border-b-gray-700" href="{{ route('user-management') }}" :active="request()->routeIs('user-management')">
                                <p class="fa-solid fa-user-gear text-2xl ml-2 mr-2 text-white"></p>
                                <p class="sidebar-text">{{ __('Usuarios') }}</p>
                            </x-nav-link>
                        </div>
                    </div>
                    <x-nav-link class="text-lg sidebar-field w-full text-left text-white" href="{{ route('student-attendance') }}" :active="request()->routeIs('student-attendance')">
                        <p class="fa-regular fa-calendar-check text-2xl ml-3 mr-2 text-white"></p>
                        <p class="sidebar-text">{{ __('Asistencia') }}</p>
                    </x-nav-link>
                    <x-nav-link class="text-lg sidebar-field w-full text-left text-white" href="{{ route('tasks') }}" :active="request()->routeIs('tasks')">
                        <p class="fa-regular fa-file text-2xl ml-3 mr-2 text-white"></p>
                        <p class="sidebar-text">{{ __('Tareas') }}</p>
                    </x-nav-link>
                    <x-nav-link class="text-lg sidebar-field w-full text-left text-white" href="{{ route('games') }}" :active="request()->routeIs('games')">
                        <p class="fa-solid fa-gamepad text-2xl ml-2 mr-2 text-white"></p>
                        <p class="sidebar-text">{{ __('Juegos') }}</p>
                    </x-nav-link>
                </div>
                <div>
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="text-lg sidebar-field w-full text-left text-white">
                            <span class="fa-solid fa-arrow-right-from-bracket text-2xl ml-3 mr-2 text-white"></span>
                            <p class="sidebar-text">{{ __('Log Out') }}</p>
                        </x-dropdown-link>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        #sidebar {
            width: 190px;
        }

        #sidebar .sidebar-field {
            padding: 0.7rem;
            overflow: hidden;
        }

        #sidebar .sidebar-field .sidebar-text {
            white-space: nowrap;
        }

        .profile-photo {
            width: 30px;
            height: 30px;
        }
    </style>
</div>
