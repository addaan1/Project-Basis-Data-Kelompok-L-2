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
        Schema::table('inventories', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('inventories', 'jenis_beras')) {
                $table->string('jenis_beras')->nullable()->after('id_inventory');
            }
            if (!Schema::hasColumn('inventories', 'kualitas')) {
                $table->string('kualitas')->nullable()->after('jenis_beras');
            }
            if (!Schema::hasColumn('inventories', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('kualitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['jenis_beras', 'kualitas', 'keterangan']);
        });
    }
};
