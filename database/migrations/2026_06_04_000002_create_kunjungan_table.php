<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('toko_id');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('jarak_meter', 10, 2);
            $table->string('barcode_scanned', 50);
            $table->boolean('gps_valid')->default(false);
            $table->boolean('barcode_valid')->default(false);
            $table->enum('status', ['valid', 'invalid'])->default('invalid');
            $table->text('catatan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamp('waktu_kunjungan')->useCurrent();
            $table->timestamps();

            $table->foreign('toko_id')->references('id')->on('toko')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['toko_id']);
        });

        Schema::dropIfExists('kunjungan');
    }
};
