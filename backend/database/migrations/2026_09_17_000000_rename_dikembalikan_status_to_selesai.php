<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Replace the legacy returned status with the completed status.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('diajukan', 'dipinjam', 'dikembalikan', 'selesai', 'telat') NOT NULL DEFAULT 'diajukan'");
        }

        DB::table('peminjaman')->where('status', 'dikembalikan')->update(['status' => 'selesai']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('diajukan', 'dipinjam', 'selesai', 'telat') NOT NULL DEFAULT 'diajukan'");
        }
    }

    /**
     * Restore the legacy status when rolling back.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('diajukan', 'dipinjam', 'dikembalikan', 'selesai', 'telat') NOT NULL DEFAULT 'diajukan'");
        }

        DB::table('peminjaman')->where('status', 'selesai')->update(['status' => 'dikembalikan']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('diajukan', 'dipinjam', 'dikembalikan', 'telat') NOT NULL DEFAULT 'diajukan'");
        }
    }
};
