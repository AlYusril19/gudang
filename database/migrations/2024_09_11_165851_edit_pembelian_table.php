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
        Schema::table('pembelian', function (Blueprint $table) {
            // Menambahkan kolom baru
            $table->decimal('total_harga', 15, 0)->nullable(); // Kolom total harga untuk pembelian
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('supplier')->onDelete('cascade');
            // Hapus foreign key constraint
            $table->dropForeign(['barang_id']);
            $table->dropColumn('jumlah');    // Menghapus kolom jumlah yang tidak diperlukan lagi
            $table->dropColumn('harga_beli');// Menghapus kolom harga_jual karena akan disimpan di tabel detail
            
            // Hapus kolom barang_id
            $table->dropColumn('barang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            // Mengembalikan perubahan jika rollback
            $table->dropColumn('total_harga');
            $table->unsignedBigInteger('barang_id');
            $table->integer('jumlah');
            $table->decimal('harga_beli', 15, 0);
        });
    }
};
