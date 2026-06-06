<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->bigIncrements('id_penjualan');
            $table->timestamp('timestamp')->useCurrent();
            $table->integer('total');
        });

        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->bigIncrements('idpenjualan_detail');
            $table->unsignedBigInteger('id_penjualan');
            $table->string('id_barang', 8);
            $table->integer('jumlah');
            $table->integer('subtotal');

            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->cascadeOnDelete();
            $table->foreign('id_barang')->references('id_barang')->on('barang')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->dropForeign(['id_penjualan']);
            $table->dropForeign(['id_barang']);
        });

        Schema::dropIfExists('penjualan_detail');
        Schema::dropIfExists('penjualan');
    }
};
