<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $fillable = ['nim', 'nama', 'serial_nfc'];

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
