<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamPengembalianTest extends TestCase
{
    use RefreshDatabase;

    public function test_peminjam_can_return_borrowed_item(): void
    {
        $this->withoutMiddleware();

        $peminjam = User::create([
            'name' => 'Peminjam Uji',
            'email' => 'peminjam_return@test.com',
            'password' => bcrypt('password'),
            'role' => 'peminjam',
        ]);

        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);

        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Laptop',
            'stok' => 2,
            'status_kondisi' => 'baik',
            'deskripsi' => 'Laptop untuk testing',
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tgl_pinjam' => '2026-09-01',
            'tgl_kembali_plan' => '2026-09-05',
            'status' => 'dipinjam',
        ]);

        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($peminjam)->post(route('peminjam.peminjaman.kembalikan', $peminjaman->id));

        $response->assertRedirect(route('peminjam.riwayat'));

        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => 'Belum diperiksa',
            'denda' => 0,
            'petugas_id' => $peminjam->id,
        ]);

        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'status' => 'selesai',
        ]);

        $alat->refresh();
        $this->assertSame(3, $alat->stok);
    }
}
