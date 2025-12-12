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
            if (!Schema::hasColumn('negosiasis', 'id_produk')) {
                $table->foreignId('id_produk')->after('id')->constrained('produk_beras', 'id_produk')->onDelete('cascade');
            }
            // Also adding other missing columns if any, based on controller usage
            if (!Schema::hasColumn('negosiasis', 'harga_penawaran')) {
                $table->decimal('harga_penawaran', 15, 2)->after('id_petani');
            }
            if (!Schema::hasColumn('negosiasis', 'harga_awal')) {
                $table->decimal('harga_awal', 15, 2)->nullable()->after('harga_penawaran');
            }
            if (!Schema::hasColumn('negosiasis', 'jumlah_kg')) {
                $table->integer('jumlah_kg')->default(1)->after('harga_awal');
            }
            if (!Schema::hasColumn('negosiasis', 'pesan')) {
                $table->text('pesan')->nullable()->after('jumlah_kg');
            }
            if (!Schema::hasColumn('negosiasis', 'status')) {
                $table->string('status')->default('dalam_proses')->after('pesan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('negosiasis', function (Blueprint $table) {
            $table->dropForeign(['id_produk']);
            $table->dropColumn(['id_produk', 'harga_penawaran', 'harga_awal', 'jumlah_kg', 'pesan', 'status']);
        });
    }
};
