<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>weidit | Dashboard Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">

        <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
            @if(auth()->user()->role === 'admin')
                PANEL ADMIN
            @elseif(auth()->user()->role === 'petugas')
                PANEL PETUGAS
            @elseif(auth()->user()->role === 'peminjam')
                PANEL PEMINJAM
            @else
                PANEL
            @endif
        </div>

        <nav class="flex-1 p-4 space-y-2">

            <!-- MENU ADMIN -->
            @if(auth()->user()->role === 'admin')

                <a href="{{ route('admin.dashboard') }}"
                    class="block px-4 py-2 rounded-lg
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-gray-800 text-white font-medium'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Dashboard
                </a>

                <a href="{{ route('admin.user.index') }}"
                    class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    Kelola User
                </a>

                <a href="{{ route('admin.kategori.index') }}"
                    class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    Kelola Kategori
                </a>

                <a href="{{ route('admin.alat.index') }}"
                    class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    Kelola Alat
                </a>

                <a href="{{ route('admin.peminjaman.index') }}"
                    class="block px-4 py-2 rounded-lg transition
                    {{ request()->routeIs('admin.peminjaman.*')
                        ? 'bg-gray-800 text-white font-medium shadow'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Kelola Peminjaman
                </a>

                <a href="{{ route('admin.pengembalian.index') }}"
                    class="block px-4 py-2 rounded-lg transition
                    {{ request()->routeIs('admin.pengembalian.*')
                        ? 'bg-gray-800 text-white font-medium shadow'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Kelola Pengembalian
                </a>

            @endif


            <!-- MENU PETUGAS -->
            @if(auth()->user()->role === 'petugas')

                <a href="{{ route('petugas.peminjaman.index') }}"
                    class="block px-4 py-2 rounded-lg transition
                    {{ request()->routeIs('petugas.peminjaman.*')
                        ? 'bg-gray-800 text-white font-medium shadow'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Persetujuan Peminjaman
                </a>

                <a href="{{ route('petugas.pengembalian.index') }}"
                    class="block px-4 py-2 rounded-lg transition
                    {{ request()->routeIs('petugas.pengembalian.*')
                        ? 'bg-gray-800 text-white font-medium shadow'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Pemantauan Pengembalian
                </a>

                <a href="{{ route('petugas.laporan.index') }}"
                    class="block px-4 py-2 rounded-lg transition
                    {{ request()->routeIs('petugas.laporan.*')
                        ? 'bg-gray-800 text-white font-medium shadow'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Cetak Laporan
                </a>

            @endif

            <!-- MENU PEMINJAM -->
            @if(auth()->user()->role === 'peminjam')

                <a href="{{ route('peminjam.katalog') }}"
                    class="block px-4 py-2 rounded-lg transition
                    {{ request()->routeIs('peminjam.katalog')
                        ? 'bg-gray-800 text-white font-medium shadow'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Katalog
                </a>

                <a href="{{ route('peminjam.riwayat') }}"
                    class="block px-4 py-2 rounded-lg transition
                    {{ request()->routeIs('peminjam.riwayat')
                        ? 'bg-gray-800 text-white font-medium shadow'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Riwayat Peminjam
                </a>

            @endif

        </nav>

        <div class="p-4 border-t border-gray-800 text-gray-400">
            Logged in as:
            <span class="text-white font-semibold">
                {{ auth()->user()->name }}
            </span>
        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-auto">

        <!-- NAVBAR -->
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">

            <div class="text-lg font-semibold text-gray-800">
                @hasSection('header-title')
                    @yield('header-title')
                @else
                    @if(auth()->user()->role === 'admin')
                        PANEL ADMIN
                    @elseif(auth()->user()->role === 'petugas')
                        PANEL PETUGAS
                    @elseif(auth()->user()->role === 'peminjam')
                        PANEL PEMINJAM
                    @else
                        Dashboard
                    @endif
                @endif
            </div>

            <div>
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf

                    <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Logout
                    </button>
                </form>
            </div>

        </header>


        <!-- KONTEN HALAMAN -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.logout-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!confirm('Anda yakin ingin logout?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
</html>