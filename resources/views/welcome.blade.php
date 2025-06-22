<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8 bg-gray-100">
        <div class="w-full sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl space-y-8 bg-white shadow-lg rounded-lg p-8 md:p-10 lg:p-12">
            
            <!-- Logo -->
            <div class="flex justify-center">
                <x-application-logo class="w-24 h-24 md:w-32 md:h-32" />
            </div>

            <!-- Judul -->
            <h2 class="mt-6 text-center text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800">
                Selamat Datang di <span class="text-indigo-600">BusAcces</span>
            </h2>

            <!-- Tombol Login -->
            <div class="flex justify-center">
                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto px-6 py-3 text-white text-lg bg-indigo-600 hover:bg-indigo-700 rounded-md font-medium shadow-md transition duration-200 ease-in-out text-center">
                    Masuk Sekarang
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>
