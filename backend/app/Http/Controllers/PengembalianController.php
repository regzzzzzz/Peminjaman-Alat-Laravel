<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    /**
     * Menampilkan daftar peminjaman yang masih dipinjam
     */
    public function index()
    {
        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])
        ->where('status', 'dipinjam')
        ->latest()
        ->get();

        return view(
            'petugas.pengembalian.index',
            compact('peminjamans')
        );
    }

    /**
     * Menampilkan form pengembalian
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])->findOrFail($id);

        // Hanya peminjaman dengan status dipinjam
        if ($peminjaman->status !== 'dipinjam') {
            return redirect()
                ->route('petugas.pengembalian.index')
                ->with(
                    'error',
                    'Peminjaman ini tidak dapat dikembalikan.'
                );
        }

        // Cegah pengembalian dua kali
        if ($peminjaman->pengembalian()->exists()) {
            return redirect()
                ->route('petugas.pengembalian.index')
                ->with(
                    'error',
                    'Peminjaman ini sudah dikembalikan.'
                );
        }

        return view(
            'petugas.pengembalian.show',
            compact('peminjaman')
        );
    }

    /**
     * Menyimpan pengembalian
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string|max:255',
            'denda' => 'required|integer|min:0',
        ], [
            'kondisi_kembali.required' =>
                'Kondisi alat harus diisi.',

            'denda.required' =>
                'Denda harus diisi.',

            'denda.integer' =>
                'Denda harus berupa angka.',

            'denda.min' =>
                'Denda tidak boleh kurang dari 0.',
        ]);

        DB::transaction(function () use ($request, $id) {

            // Ambil peminjaman dan detail alat
            $peminjaman = Peminjaman::with([
                'detailPinjams.alat'
            ])
            ->lockForUpdate()
            ->findOrFail($id);

            // Pastikan status masih dipinjam
            if ($peminjaman->status !== 'dipinjam') {
                abort(
                    400,
                    'Peminjaman ini sudah tidak dapat dikembalikan.'
                );
            }

            // Pastikan belum ada pengembalian
            if ($peminjaman->pengembalian()->exists()) {
                abort(
                    400,
                    'Peminjaman ini sudah memiliki data pengembalian.'
                );
            }

            /*
             * 1. Simpan data pengembalian
             */
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now()->format('Y-m-d'),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda,
                'petugas_id' => auth()->id(),
            ]);

            /*
             * 2. Kembalikan stok alat
             */
            foreach ($peminjaman->detailPinjams as $detail) {

                $alat = $detail->alat;

                if ($alat) {
                    $alat->increment(
                        'stok',
                        $detail->jumlah
                    );
                }
            }

            /*
             * 3. Ubah status peminjaman
             */
            $peminjaman->update([
                'status' => 'selesai',
            ]);
        });

        return redirect()
            ->route('petugas.pengembalian.index')
            ->with(
                'success',
                'Pengembalian berhasil diproses dan stok alat telah dikembalikan.'
            );
    }
}
