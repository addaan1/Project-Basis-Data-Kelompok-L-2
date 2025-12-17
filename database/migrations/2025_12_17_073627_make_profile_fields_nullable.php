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
        Schema::table('petanis', function (Blueprint $table) {
            $table->string('lokasi')->nullable()->change();
            $table->string('kontak')->nullable()->change();
            $table->integer('kapasitas_panen')->nullable()->change();
        });

        Schema::table('pengepuls', function (Blueprint $table) {
            $table->string('lokasi')->nullable()->change();
            $table->integer('kapasitas_tampung')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petanis', function (Blueprint $table) {
            // Reverting requires knowing the original state, assuming they were strictly required.
            // Be careful if data with nulls exists, this might fail.
            // For now, we attempt to revert to non-nullable if possible, or just leave as is.
             $table->string('lokasi')->nullable(false)->change();
             $table->string('kontak')->nullable(false)->change();
             $table->integer('kapasitas_panen')->nullable(false)->change();
        });

        Schema::table('pengepuls', function (Blueprint $table) {
             $table->string('lokasi')->nullable(false)->change();
             $table->integer('kapasitas_tampung')->nullable(false)->change();
        });
    }
};
