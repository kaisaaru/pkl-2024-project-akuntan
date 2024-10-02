<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Kategori extends Model
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

        // Menambahkan event creating untuk mengatur kode_kategori otomatis
        static::creating(function ($kategori) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel kategori
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur kode_kategori dengan format "B00(id)"
                $kategori->kode_kategori = 'K000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur kode_kategori dengan format "B00(id)"
                $kategori->kode_kategori = 'K00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur kode_kategori dengan format "B00(id)"
                $kategori->kode_kategori = 'K0' . $nextId . '';
            }
        });
    }
    public function barang()
    {
        return $this->hasMany(Barang::class, 'kategori', 'kode_kategori');
        // return $this->hasMany(BukuBesar::class, 'tipe');
    }
}
