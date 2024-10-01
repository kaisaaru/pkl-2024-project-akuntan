<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RiwayatBukuBesar extends Model
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

    // Menambahkan event creating untuk mengatur kode_riwayat otomatis
    static::creating(function ($riwayat) {
        $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel Barang
        $nextId = $lastId + 1; // Menghitung id berikutnya

        if($nextId < 10){
        // Mengatur kode_riwayat dengan format "B00(id)"
        $riwayat->kode_riwayat = 'R000' . $nextId . '';
        } else if($nextId < 100){
            // Mengatur kode_riwayat dengan format "B00(id)"
            $riwayat->kode_riwayat = 'R00' . $nextId . '';
        } else if($nextId < 1000){
            // Mengatur kode_riwayat dengan format "B00(id)"
            $riwayat->kode_riwayat = 'R0' . $nextId . '';
        }
    });
    }
}
