<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi')->constrained('transaksis', 'id_transaksi')->onDelete('cascade');
            $table->string('status_before')->nullable();
            $table->string('status_after');
            $table->foreignId('changed_by')->constrained('users', 'id_user');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_histories');
    }
};