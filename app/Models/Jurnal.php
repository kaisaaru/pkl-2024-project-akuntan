<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;

    protected $hidden = [
        'remember_token',
    ];
    protected $guarded = [
        'id',
        '_token',
    ];
    protected $dates = ['created_at'];
    protected $table = 'jurnals';
    protected static function boot()
    {
        parent::boot();

        // Menambahkan event creating untuk mengatur barang_id otomatis
        static::creating(function ($barang) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel Barang
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur barang_id dengan format "B00(id)"
                $barang->id_jurnal = 'J-000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur barang_id dengan format "B00(id)"
                $barang->id_jurnal = 'J-00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur barang_id dengan format "B00(id)"
                $barang->id_jurnal = 'J-0' . $nextId . '';
            }
        });
    }
}
