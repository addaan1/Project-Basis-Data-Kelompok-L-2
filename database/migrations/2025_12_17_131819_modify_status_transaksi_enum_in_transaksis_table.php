<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Using raw SQL because Schema builder doesn't support modifying ENUMs easily
            DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_transaksi ENUM('pending', 'confirmed', 'cancelled', 'disetujui', 'ditolak', 'menunggu_pembayaran', 'negosiasi', 'dalam_proses', 'completed') DEFAULT 'menunggu_pembayaran'");
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
             DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_transaksi ENUM('pending', 'confirmed', 'cancelled')");
        });
    }
};
