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
        // Pastikan menggunakan koneksi default
        Schema::create('fact_user_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('user_id'); // Referensi logis ke DB utama
            $table->string('role'); // petani, pengepul
            
            // Metrics
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->integer('total_kg_sold')->default(0);
            $table->integer('total_kg_bought')->default(0);
            $table->integer('transaction_count')->default(0);
            
            $table->timestamps();
            
            // Index untuk mempercepat query dashboard
            $table->index(['user_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_user_daily_metrics');
    }
};
