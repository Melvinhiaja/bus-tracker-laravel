<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route(
                    Auth::user()->role === 'admin' ? 'admin.dashboard' :
                    (Auth::user()->role === 'karyawan' ? 'karyawan.dashboard' : 'driver.index')
                ) }}">
                    <x-application-logo class="block h-8 w-auto fill-current text-gray-800 dark:text-white" />
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden sm:flex sm:space-x-6 ms-10">
                    <x-nav-link :href="route(
                        Auth::user()->role === 'admin' ? 'admin.dashboard' :
                        (Auth::user()->role === 'karyawan' ? 'karyawan.dashboard' : 'driver.index')
                    )"
                        :active="request()->routeIs('admin.dashboard') || request()->routeIs('karyawan.dashboard') || request()->routeIs('driver.index')">
                        Dashboard
                    </x-nav-link>

                    @if(Auth::user()->role === 'admin')
                    <x-nav-link :href="route('admin.createKaryawan')" :active="request()->routeIs('admin.createKaryawan')">Tambah Karyawan</x-nav-link>
                    <x-nav-link :href="route('admin.createAdmin')" :active="request()->routeIs('admin.createAdmin')">Tambah Admin</x-nav-link>
                    <x-nav-link :href="route('admin.createBus')" :active="request()->routeIs('admin.createBus')">Tambah Bus</x-nav-link>
                    <x-nav-link :href="route('admin.alasan.index')" :active="request()->routeIs('admin.alasan.index')">Kelola Alasan</x-nav-link>
                    <x-nav-link :href="route('admin.desa.index')" :active="request()->routeIs('admin.desa.index')">Kelola Desa</x-nav-link>
                    <x-nav-link :href="route('admin.perjalanan.index')" :active="request()->routeIs('admin.perjalanan.index')">Kelola Perjalanan</x-nav-link>

                    @elseif(Auth::user()->role === 'karyawan')
                    <x-nav-link :href="route('karyawan.penumpang')" :active="request()->routeIs('karyawan.penumpang')">Daftar Penumpang</x-nav-link>
                    <x-nav-link :href="route('karyawan.kelolaPenumpang')" :active="request()->routeIs('karyawan.kelolaPenumpang')">Kelola Penumpang</x-nav-link>
                    <x-nav-link :href="route('karyawan.tiket.create')" :active="request()->routeIs('karyawan.tiket.create')">Pesan Tiket</x-nav-link>
                    <x-nav-link :href="route('karyawan.tiket')" :active="request()->routeIs('karyawan.tiket')">Cetak Tiket</x-nav-link>
                    <x-nav-link :href="route('karyawan.tracking')" :active="request()->routeIs('karyawan.tracking')">Monitor Bus Aktif</x-nav-link>
                    <x-nav-link :href="route('driver.index')" :active="request()->routeIs('driver.index')">Perjalanan Aktif</x-nav-link>

                    @elseif(Auth::user()->role === 'driver')
                    <x-nav-link :href="route('driver.scanTiket')" :active="request()->routeIs('driver.scanTiket')">
        Scan Tiket
    </x-nav-link>


                    @endif
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center space-x-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0L5.293 8.707a1 1 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div :class="{ 'block': open, 'hidden': !open }" class="sm:hidden hidden px-4 pt-4 pb-3">
        <x-responsive-nav-link :href="route(
            Auth::user()->role === 'admin' ? 'admin.dashboard' :
            (Auth::user()->role === 'karyawan' ? 'karyawan.dashboard' : 'driver.index')
        )"
            :active="request()->routeIs('admin.dashboard') || request()->routeIs('karyawan.dashboard') || request()->routeIs('driver.index')">
            Dashboard
        </x-responsive-nav-link>

        @if(Auth::user()->role === 'admin')
        <x-responsive-nav-link :href="route('admin.createKaryawan')">Tambah Karyawan</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('admin.createAdmin')">Tambah Admin</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('admin.createBus')">Tambah Bus</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('admin.alasan.index')">Kelola Alasan</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('admin.desa.index')">Kelola Desa</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('admin.perjalanan.index')">Kelola Perjalanan</x-responsive-nav-link>
        @elseif(Auth::user()->role === 'karyawan')
        <x-responsive-nav-link :href="route('karyawan.penumpang')">Daftar Penumpang</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('karyawan.kelolaPenumpang')">Kelola Penumpang</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('karyawan.tiket.create')">Pesan Tiket</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('karyawan.tiket')">Cetak Tiket</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('karyawan.tracking')">Monitor Bus</x-responsive-nav-link>
        <x-nav-link :href="route('driver.index')" :active="request()->routeIs('driver.index')">Perjalanan Aktif</x-nav-link>
        @elseif(Auth::user()->role === 'driver')
        <x-responsive-nav-link :href="route('driver.scanTiket')">Scan Tiket</x-responsive-nav-link>


 
        @endif

        <!-- Mobile Profile Info -->
        <div class="border-t border-gray-200 dark:border-gray-600 mt-4 pt-3">
            <div class="text-sm text-gray-600 dark:text-gray-300">{{ Auth::user()->name }}</div>
            <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>

            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                    Logout
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>