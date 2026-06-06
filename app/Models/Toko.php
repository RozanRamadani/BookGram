<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Toko extends Model
{
    protected $table = 'toko';

    protected $fillable = [
        'nama_toko',
        'alamat',
        'latitude',
        'longitude',
        'kode_barcode',
        'radius_meter',
    ];

    protected $casts = [
        'latitude'     => 'decimal:8',
        'longitude'    => 'decimal:8',
        'radius_meter' => 'integer',
    ];

    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'toko_id');
    }

    /**
     * Generate a unique barcode code for a new toko.
     * Format: TOKO-XXXXXX (6 random uppercase alphanumeric chars)
     */
    public static function generateKodeBarcode(): string
    {
        do {
            $kode = 'TOKO-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (self::where('kode_barcode', $kode)->exists());

        return $kode;
    }
}
