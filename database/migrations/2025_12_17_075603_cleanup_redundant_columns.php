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
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'user_id')) {
                 // Drop foreign key if exists first using array syntax to auto-detect index
                 // Or by name if known. Array is safer given auto-generated names.
                 // We wrapped in try-catch in case FK doesn't exist or is named differently
                 try {
                     $table->dropForeign(['user_id']);
                 } catch (\Exception $e) {
                     // FK might not exist or be different named, continue to drop column
                 }
                 $table->dropColumn('user_id');
            }
        });

        Schema::table('produk_beras', function (Blueprint $table) {
            if (Schema::hasColumn('produk_beras', 'id_user')) {
                // Assuming it might have index but no FK constraint explicit in earlier files?
                // Step 14 code shows: $table->unsignedBigInteger('id_user')->nullable(); no constraint
                $table->dropColumn('id_user');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
             $table->foreignId('user_id')->nullable()->constrained('users', 'id_user');
        });

        Schema::table('produk_beras', function (Blueprint $table) {
             $table->unsignedBigInteger('id_user')->nullable();
        });
    }
};
