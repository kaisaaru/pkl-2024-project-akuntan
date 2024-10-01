<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BukuBesar extends Model
{
    use HasFactory;

    protected $fillable = ['debet', 'kredit', 'tipe', 'ket'];
    public function subBukuBesar()
    {
        return $this->hasMany(SubBukuBesar::class, 'no_bukubesar', 'no_bukubesar');
    }

    public function tipeAkun()
    {
        return $this->hasMany(TipeAkun::class, 'tipe', 'tipe');
        // return $this->belongsTo(TipeAkun::class, 'tipe');
    }
}
