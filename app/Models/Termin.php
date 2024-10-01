<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Termin extends Model
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

    protected static function boot()
    {
        parent::boot();

        // Menambahkan event creating untuk mengatur kode_termin otomatis
        static::creating(function ($termin) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel termin
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur kode_termin dengan format "B00(id)"
                $termin->kode_termin = 'T-000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur kode_termin dengan format "B00(id)"
                $termin->kode_termin = 'T-00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur kode_termin dengan format "B00(id)"
                $termin->kode_termin = 'T-0' . $nextId . '';
            }
        });
    }
}
