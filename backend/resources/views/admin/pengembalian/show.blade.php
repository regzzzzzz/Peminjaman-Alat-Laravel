@extends('layouts.app')

@section('title', 'Detail Pengembalian - Panel Admin')
@section('header-title', 'Detail Pengembalian')

@section('content')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl mx-auto">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Detail Pengembalian</h3>

        <div class="mb-6 space-y-2 text-sm text-gray-700">
            <p><span class="font-semibold">Peminjam:</span> {{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}</p>
            <p><span class="font-semibold">Tanggal Pinjam:</span> {{ $pengembalian->peminjaman->tgl_pinjam }}</p>
            <p><span class="font-semibold">Tanggal Kembali:</span> {{ $pengembalian->tgl_kembali }}</p>
            <p><span class="font-semibold">Kondisi Kembali:</span> {{ $pengembalian->kondisi_kembali }}</p>
            <p><span class="font-semibold">Denda:</span> Rp {{ number_format($pengembalian->denda,0,',','.') }}</p>
            <p><span class="font-semibold">Petugas:</span> {{ $pengembalian->petugas->name ?? 'Petugas Dihapus' }}</p>
            <p><span class="font-semibold">Alat:</span></p>
            <ul class="list-disc list-inside ml-4">
                @foreach($pengembalian->peminjaman->detailPinjams as $detail)
                    <li>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $detail->jumlah }} pcs)</li>
                @endforeach
            </ul>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.pengembalian.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-5 py-2.5 rounded-lg transition">Kembali</a>
            <a href="{{ route('admin.pengembalian.edit', $pengembalian->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-5 py-2.5 rounded-lg transition">Edit</a>
        </div>
    </div>
@endsection
