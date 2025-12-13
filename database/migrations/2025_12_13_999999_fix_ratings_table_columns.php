<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Truncate table to avoid NOT NULL constraint errors if bad data exists
        if (Schema::hasTable('ratings')) {
            DB::table('ratings')->truncate();
        }

        Schema::table('ratings', function (Blueprint $table) {
            if (!Schema::hasColumn('ratings', 'id_produk')) {
                $table->unsignedBigInteger('id_produk')->after('id');
                $table->foreign('id_produk')->references('id_produk')->on('produk_beras')->onDelete('cascade');
            }
            if (!Schema::hasColumn('ratings', 'id_user')) {
                $table->unsignedBigInteger('id_user')->after('id_produk');
                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('ratings', 'id_penjual')) {
                $table->unsignedBigInteger('id_penjual')->nullable()->after('id_user');
                $table->foreign('id_penjual')->references('id_user')->on('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('ratings', 'nilai_rating')) {
                $table->integer('nilai_rating')->default(0)->after('id_penjual');
            }
            if (!Schema::hasColumn('ratings', 'komentar')) {
                $table->text('komentar')->nullable()->after('nilai_rating');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Drop FKs first
            $table->dropForeign(['id_produk']);
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_penjual']);
            
            $table->dropColumn(['id_produk', 'id_user', 'id_penjual', 'nilai_rating', 'komentar']);
        });
    }
};
