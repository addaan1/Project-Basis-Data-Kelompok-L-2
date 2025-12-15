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
        if (!Schema::hasColumn('transaksis', 'id_produk')) {
            Schema::table('transaksis', function (Blueprint $table) {
                // Gunakan onDelete('set null') atau 'cascade' sesuai kebutuhan
                // Kami anggap cascade agar jika produk dihapus, history transaksi hilang (dummy logic)
                // Atau set null agar history tetap ada.
                $table->foreignId('id_produk')->nullable()->constrained('produk_beras', 'id_produk')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('transaksis', 'id_produk')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->dropForeign(['id_produk']);
                $table->dropColumn('id_produk');
            });
        }
    }
};
