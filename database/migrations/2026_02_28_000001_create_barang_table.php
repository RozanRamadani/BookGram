<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->string('id_barang', 8)->primary();
            $table->string('nama', 50);
            $table->integer('harga');
            $table->timestamp('timestamp')->useCurrent();
        });

        // Create trigger to auto-generate id_barang
        DB::unprepared('
            CREATE TRIGGER trigger_id_barang
            BEFORE INSERT ON barang FOR EACH ROW
            BEGIN
                DECLARE nr INTEGER DEFAULT 0;
                SET nr = (
                    SELECT COUNT(id_barang) FROM barang
                    WHERE DAY(timestamp) = DAY(CURRENT_TIMESTAMP)
                      AND MONTH(timestamp) = MONTH(CURRENT_TIMESTAMP)
                      AND YEAR(timestamp) = YEAR(CURRENT_TIMESTAMP)
                ) + 1;
                SET NEW.id_barang = CONCAT(
                    RIGHT(YEAR(CURRENT_TIMESTAMP), 2),
                    LPAD(MONTH(CURRENT_TIMESTAMP), 2, \'0\'),
                    LPAD(DAY(CURRENT_TIMESTAMP), 2, \'0\'),
                    LPAD(nr, 2, \'0\')
                );
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_id_barang');
        Schema::dropIfExists('barang');
    }
};
