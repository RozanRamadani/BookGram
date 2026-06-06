<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $fillable = [
        'user_id',
        'toko_id',
        'latitude',
        'longitude',
        'jarak_meter',
        'barcode_scanned',
        'gps_valid',
        'barcode_valid',
        'status',
        'catatan',
        'foto',
        'waktu_kunjungan',
    ];

    protected $casts = [
        'latitude'         => 'decimal:8',
        'longitude'        => 'decimal:8',
        'jarak_meter'      => 'decimal:2',
        'gps_valid'        => 'boolean',
        'barcode_valid'    => 'boolean',
        'waktu_kunjungan'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }
}
