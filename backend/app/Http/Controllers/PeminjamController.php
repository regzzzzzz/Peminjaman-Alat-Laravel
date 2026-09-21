<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat()
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();
        return view('peminjam.katalog', compact('alats'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => 'required|date|after:today',
            'alat_id' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // Buat header peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            // Masukkan daftar alat yang dipinjam ke detail_pinjam
            foreach ($request->alat_id as $index => $alatId) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $request->jumlah[$index],
                ]);
            }

            DB::commit();
            return redirect()->route('peminjam.riwayat')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with('detailPinjams.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjamans'));
    }


public function kembalikanPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjams.alat')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
            return redirect()->route('peminjam.riwayat')->with('error', 'Peminjaman ini tidak bisa dikembalikan saat ini.');
        }

        if ($peminjaman->pengembalian()->exists()) {
            return redirect()->route('peminjam.riwayat')->with('error', 'Alat ini sudah pernah dikembalikan.');
        }

        DB::beginTransaction();

        try {
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => 'Belum diperiksa',
                'denda' => 0,
                'petugas_id' => auth()->id(),
            ]);

            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = $detail->alat;
                if ($alat) {
                    $alat->stok += $detail->jumlah;
                    $alat->save();
                }
            }

            $peminjaman->update(['status' => 'selesai']);

            DB::commit();

            return redirect()->route('peminjam.riwayat')->with('success', 'Alat berhasil dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengembalikan alat: ' . $e->getMessage());
        }
    }
}
