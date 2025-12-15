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
        // 1. Dimension Tables
        
        // Dim Waktu (Time Dimension)
        Schema::create('dim_waktu', function (Blueprint $table) {
            $table->id('id_waktu');
            $table->date('tanggal')->unique();
            $table->integer('hari');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('kuartal');
            $table->string('nama_hari');
            $table->string('nama_bulan');
            $table->boolean('is_akhir_pekan')->default(false);
            $table->timestamps();
        });

        // Dim User (Petani, Pengepul, dsb) - Type 2 SCD support fields included (versioning)
        Schema::create('dim_users', function (Blueprint $table) {
            $table->id('sk_user'); // Surrogate Key
            $table->unsignedBigInteger('id_user_asli'); // Original ID from users table
            $table->string('nama');
            $table->string('peran'); // petani, pengepul, distributor, dll
            $table->string('lokasi')->nullable(); // Jika ada data lokasi
            $table->timestamps();
            
            // Indexing for faster lookup during ETL
            $table->index('id_user_asli');
        });

        // Dim Produk
        Schema::create('dim_produk', function (Blueprint $table) {
            $table->id('sk_produk'); // Surrogate Key
            $table->unsignedBigInteger('id_produk_asli');
            $table->string('nama_produk');
            $table->string('jenis_beras')->nullable();
            $table->string('kualitas')->nullable(); 
            $table->timestamps();
            
            $table->index('id_produk_asli');
        });

        // 2. Fact Tables

        // Fact Transaksi (Sales & Purchase) -> Focus: GMV, Revenue, Spending
        Schema::create('fact_transaksi', function (Blueprint $table) {
            $table->id('id_fact_sales');
            
            // Foreign Keys to Dimensions
            $table->unsignedBigInteger('sk_waktu');
            $table->unsignedBigInteger('sk_penjual');
            $table->unsignedBigInteger('sk_pembeli');
            $table->unsignedBigInteger('sk_produk')->nullable();
            
            // Measures / Metrics
            $table->decimal('jumlah_kg', 15, 2)->default(0);
            $table->decimal('nilai_transaksi', 15, 2)->default(0); // Harga Akhir
            $table->decimal('harga_per_kg', 15, 2)->default(0);
            
            // Attributes for filtering
            $table->string('jenis_transaksi'); // jual, beli
            $table->string('status_transaksi'); // confirmed only usually, but good to have
            $table->string('no_transaksi_asli'); // reference

            $table->timestamps();

            // Indexes
            $table->index('sk_waktu');
            $table->index('sk_penjual');
            $table->index('sk_pembeli');
        });

        // Fact Stok Snapshot -> Focus: Daily Inventory Levels
        Schema::create('fact_stok_snapshot', function (Blueprint $table) {
            $table->id('id_fact_stok');
            
            $table->unsignedBigInteger('sk_waktu');
            $table->unsignedBigInteger('sk_pemilik'); // User ID (Petani/Pengepul)
            $table->unsignedBigInteger('sk_produk');
            
            // Metrics
            $table->integer('stok_akhir_hari')->default(0);
            $table->integer('stok_masuk_hari_ini')->default(0);
            $table->integer('stok_keluar_hari_ini')->default(0);
            
            $table->timestamps();
        });

        // Fact Negosiasi -> Focus: Conversion Rate, Funnel
        Schema::create('fact_negosiasi', function (Blueprint $table) {
            $table->id('id_fact_nego');
            
            $table->unsignedBigInteger('sk_waktu');
            $table->unsignedBigInteger('sk_pengaju'); // Pengepul biasanya
            $table->unsignedBigInteger('sk_penerima'); // Petani biasanya
            $table->unsignedBigInteger('sk_produk');
            
            // Metrics
            $table->decimal('harga_tawaran', 15, 2);
            $table->decimal('harga_deal', 15, 2)->nullable();
            $table->decimal('selisih_harga', 15, 2)->nullable(); // Tawaran - Deal
            
            // Status Flow
            $table->string('status_akhir'); // accepted, rejected, pending
            $table->integer('durasi_negosiasi_jam')->nullable(); // Waktu open -> closed
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_negosiasi');
        Schema::dropIfExists('fact_stok_snapshot');
        Schema::dropIfExists('fact_transaksi');
        Schema::dropIfExists('dim_produk');
        Schema::dropIfExists('dim_users');
        Schema::dropIfExists('dim_waktu');
    }
};
