@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Dashboard Peminjam')
@section('header-title', 'Riwayat Peminjaman')

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
            <h3 class="text-lg font-bold text-gray-800">Daftar riwayat peminjaman saya</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Tanggal Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamans as $item)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b">{{ $item->tgl_pinjam }}</td>
                            <td class="py-3 px-4 border-b">{{ $item->tgl_kembali_plan }}</td>
                            <td class="py-3 px-4 border-b">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if($item->status === 'diajukan') bg-yellow-100 text-yellow-700
                                    @elseif($item->status === 'dipinjam') bg-green-100 text-green-700
                                    @elseif($item->status === 'dikembalikan') bg-blue-100 text-blue-700
                                    @elseif($item->status === 'telat') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    @foreach($item->detailPinjams as $detail)
                                        <li>
                                            <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            (Jumlah: {{ $detail->jumlah }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-500">Belum ada riwayat peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
