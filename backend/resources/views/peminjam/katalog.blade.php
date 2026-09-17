@extends('layouts.app')

@section('title', 'Katalog Alat - Dashboard Peminjam')
@section('header-title', 'Katalog Alat Tersedia')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Pilih alat yang akan dipinjam</h3>
        </div>

        <div class="p-5">
            <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label for="tgl_kembali_plan" class="block text-sm font-semibold text-gray-700 mb-2">Rencana Tanggal Kembali</label>
                    <input type="date" id="tgl_kembali_plan" name="tgl_kembali_plan" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                                <th class="py-3 px-4 border-b">Pilih</th>
                                <th class="py-3 px-4 border-b">Nama Alat</th>
                                <th class="py-3 px-4 border-b">Kategori</th>
                                <th class="py-3 px-4 border-b">Stok</th>
                                <th class="py-3 px-4 border-b">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @forelse($alats as $alat)
                                <tr class="hover:bg-gray-50 transition align-top">
                                    <td class="py-3 px-4 border-b text-center">
                                        <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                    </td>
                                    <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $alat->nama_alat }}</td>
                                    <td class="py-3 px-4 border-b">{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                                    <td class="py-3 px-4 border-b">{{ $alat->stok }}</td>
                                    <td class="py-3 px-4 border-b">
                                        <input type="number" name="jumlah[]" value="1" min="1" max="{{ $alat->stok }}"
                                            class="w-24 px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada alat yang tersedia saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        Ajukan Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

