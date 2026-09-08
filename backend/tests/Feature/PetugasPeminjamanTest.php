<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasPeminjamanTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_can_view_peminjaman_page_with_related_details(): void
    {
        $petugas = User::create([
            'name' => 'Petugas Test',
            'email' => 'petugas@test.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);
        $peminjam = User::create([
            'name' => 'Peminjam Test',
            'email' => 'peminjam@test.com',
            'password' => bcrypt('password'),
            'role' => 'peminjam',
        ]);

        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Laptop',
            'stok' => 5,
            'status_kondisi' => 'baik',
            'deskripsi' => 'Test alat',
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_plan' => now()->addDays(3)->toDateString(),
            'status' => 'diajukan',
        ]);

        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($petugas)->get('/petugas/peminjaman');

        $response->assertOk();
        $response->assertSee('Daftar Pengajuan');
        $response->assertSee('Laptop');
    }
}
