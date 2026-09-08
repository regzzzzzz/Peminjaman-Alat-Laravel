<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengembalianApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_can_create_pengembalian_record_matching_migration_fields(): void
    {
        $petugas = User::create([
            'name' => 'Petugas Pengembalian',
            'email' => 'petugas_pengembalian@test.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);

        $peminjam = User::create([
            'name' => 'Peminjam Test',
            'email' => 'peminjam_pengembalian@test.com',
            'password' => bcrypt('password'),
            'role' => 'peminjam',
        ]);

        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);

        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Laptop',
            'stok' => 3,
            'status_kondisi' => 'baik',
            'deskripsi' => 'Laptop untuk testing',
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tgl_pinjam' => '2026-08-20',
            'tgl_kembali_plan' => '2026-08-25',
            'status' => 'dipinjam',
        ]);

        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($petugas, 'sanctum')->postJson('/api/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => '2026-08-24',
            'kondisi_kembali' => 'Baik',
            'denda' => 5000,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.peminjaman_id', $peminjaman->id)
            ->assertJsonPath('data.tgl_kembali', '2026-08-24')
            ->assertJsonPath('data.kondisi_kembali', 'Baik')
            ->assertJsonPath('data.denda', 5000)
            ->assertJsonPath('data.petugas_id', $petugas->id);

        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => '2026-08-24',
            'kondisi_kembali' => 'Baik',
            'denda' => 5000,
            'petugas_id' => $petugas->id,
        ]);
    }
}
