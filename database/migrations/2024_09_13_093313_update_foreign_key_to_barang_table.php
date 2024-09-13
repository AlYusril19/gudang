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
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']); // Drop foreign key lama
            $table->foreign('kategori_id')->references('id')->on('kategori')->onDelete('restrict'); // Tambah foreign key baru
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']); // Drop foreign key lama
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->onDelete('cascade');
        });
    }
};
