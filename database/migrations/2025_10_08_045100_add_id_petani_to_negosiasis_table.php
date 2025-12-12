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
        Schema::table('negosiasis', function (Blueprint $table) {
            // PERBAIKAN DI SINI: Cek dulu apakah kolom sudah ada
            if (!Schema::hasColumn('negosiasis', 'id_petani')) {
                // FIXED: id_petani references id_user on users table
                $table->foreignId('id_petani')->constrained('users', 'id_user')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('negosiasis', function (Blueprint $table) {
            // Cek dulu sebelum hapus agar tidak error saat rollback
            if (Schema::hasColumn('negosiasis', 'id_petani')) {
                // Drop foreign key dulu (biasanya format: nama_table_nama_kolom_foreign)
                // Kita gunakan array syntax agar Laravel otomatis mencari nama indexnya
                $table->dropForeign(['id_petani']); 
                $table->dropColumn('id_petani');
            }
        });
    }
};