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
        Schema::create('produk_beras', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('nama_produk');
            $table->string('jenis_beras')->nullable();
            $table->string('kualitas')->nullable();
            $table->decimal('harga', 15, 2);
            $table->integer('stok');
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->string('nama_petani')->nullable();
            $table->string('lokasi_gudang')->nullable();
            
            // Foreign Keys
            $table->unsignedBigInteger('id_petani')->nullable();
            $table->foreign('id_petani')->references('id_user')->on('users')->onDelete('cascade');
            
            // Legacy column (optional, keeping for safety if referenced elsewhere)
            $table->unsignedBigInteger('id_user')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_beras');
    }
};
