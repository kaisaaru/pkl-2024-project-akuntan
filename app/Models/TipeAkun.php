<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TipeAkun extends Model
{
    use HasFactory;

    protected $hidden = [
        'remember_token',
    ];
    protected $guarded = [
        'id', '_token',
    ];

    protected static function boot()
    {
        parent::boot();

        // Menambahkan event creating untuk mengatur barang_id otomatis
        static::creating(function ($tipeakun) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel Barang
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur barang_id dengan format "B00(id)"
                $tipeakun->kode_tipe = 'T000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur barang_id dengan format "B00(id)"
                $tipeakun->kode_tipe = 'T00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur barang_id dengan format "B00(id)"
                $tipeakun->kode_tipe = 'T0' . $nextId . '';
            }
        });
    }
    public function bukuBesar()
    {
        return $this->hasMany(BukuBesar::class, 'tipe', 'tipe');
        // return $this->hasMany(BukuBesar::class, 'tipe');
    }
}
