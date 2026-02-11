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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->date('tanggal');

            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();

            $table->decimal('lat_masuk', 10, 7)->nullable();
            $table->decimal('lng_masuk', 10, 7)->nullable();
            $table->decimal('lat_pulang', 10, 7)->nullable();
            $table->decimal('lng_pulang', 10, 7)->nullable();

            $table->string('foto_masuk')->nullable();
            $table->string('foto_pulang')->nullable();

            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir');
            $table->text('keterangan')->nullable();

            $table->string('device_id')->nullable();
            $table->enum('platform', ['web', 'android'])->default('web');

            $table->timestamps();

            // optional FK (kalau users ada di DB yang sama)
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // 1 user hanya 1 absensi per hari
            $table->unique(['user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
