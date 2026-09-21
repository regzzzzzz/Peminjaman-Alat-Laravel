<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $peminjaman = [
            [
                'user_id' => 3, // Hafidz Manis (peminjam)
                'tgl_pinjam' => '2026-06-01',
                'tgl_kembali_plan' => '2026-06-04',
                'status' => 'selesai',
            ],
            [
                'user_id' => 4, // Handy Bernard (peminjam)
                'tgl_pinjam' => '2026-06-02',
                'tgl_kembali_plan' => '2026-06-05',
                'status' => 'selesai',
            ],
            [
                'user_id' => 3, // Rubby Setrum (peminjam)
                'tgl_pinjam' => '2026-06-03',
                'tgl_kembali_plan' => '2026-06-06',
                'status' => 'telat',
            ],
            [
                'user_id' => 3,
                'tgl_pinjam' => '2026-06-08',
                'tgl_kembali_plan' => '2026-06-11',
                'status' => 'dipinjam',
            ],
            [
                'user_id' => 4,
                'tgl_pinjam' => '2026-06-09',
                'tgl_kembali_plan' => '2026-06-12',
                'status' => 'diajukan',
            ],
            [
                'user_id' => 4,
                'tgl_pinjam' => '2026-06-09',
                'tgl_kembali_plan' => '2026-06-12',
                'status' => 'diajukan',
            ],
        ];

        foreach ($peminjaman as $pinjam) {
            Peminjaman::create($pinjam);
        }
    }
}
