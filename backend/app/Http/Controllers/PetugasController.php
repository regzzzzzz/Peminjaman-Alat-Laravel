<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();
        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
        }


    public function setujuPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjam = Peminjaman::with('detailPinjams')->findOrFail($id);
            $peminjam->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjam->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
                
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_alat' => 'required|string',
            'denda' => 'nullable|integer', 
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($peminjamanId);

            // Simpan data Pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            // Update status peminjaman menjadi selesai.
            $peminjaman->update(['status' => 'selesai']);

            // kembalikan stok alat ke inventaris 
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
     
    }

    // Menolak peminjaman (menghapus pengajuan agar siswa bisa mengajukan ulang)
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Pastikan statusnya memang masih dianjukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();
        return view('petugas.pengembalian.index', compact('peminjamans', 'search'));
    }


    public function laporan(Request $request)
{
    $status = $request->input('status');
    $dari_tanggal = $request->input('dari_tanggal');
    $sampai_tanggal = $request->input('sampai_tanggal');

    $laporans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
        ->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            return $query->whereBetween('tgl_pinjam', [$dari_tanggal, $sampai_tanggal]);
        })
        ->latest()
        ->get();

    return view('petugas.laporan.index', compact('laporans', 'status', 'dari_tanggal', 'sampai_tanggal'));
}

// Menampilkan halaman khusus cetak (print preview)
public function cetakLaporan(Request $request)
{
    $status = $request->input('status');
    $dari_tanggal = $request->input('dari_tanggal');
    $sampai_tanggal = $request->input('sampai_tanggal');

    $laporans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
        ->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            return $query->whereBetween('tgl_pinjam', [$dari_tanggal, $sampai_tanggal]);
        })
        ->latest()
        ->get();

    return view('petugas.laporan.cetak', compact('laporans', 'status', 'dari_tanggal', 'sampai_tanggal'));
}

}
