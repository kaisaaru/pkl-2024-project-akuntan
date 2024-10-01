<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Kelompok extends Model
{
    use HasFactory;

    
    protected $hidden = [
        'remember_token',
    ];
    protected $guarded = [
        'id', '_token', 
    ];
    protected $dates = ['created_at'];

    protected static function boot()
    {
    parent::boot();

    // Menambahkan event creating untuk mengatur kode_kelompok otomatis
    static::creating(function ($kelompok) {
        $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel kelompok
        $nextId = $lastId + 1; // Menghitung id berikutnya

        if($nextId < 10){
        // Mengatur kode_kelompok dengan format "B00(id)"
        $kelompok->kode_kelompok = 'KL000' . $nextId . '';
        } else if($nextId < 100){
            // Mengatur kode_kelompok dengan format "B00(id)"
            $kelompok->kode_kelompok = 'KL00' . $nextId . '';
        } else if($nextId < 1000){
            // Mengatur kode_kelompok dengan format "B00(id)"
            $kelompok->kode_kelompok = 'KL0' . $nextId . '';
        }
    });
    }
}
