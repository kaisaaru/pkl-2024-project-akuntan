<?php

namespace App\Models;

use App\Models\opline;
use App\Models\detail_op;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OrderPenjualan extends Model
{
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

        // Menambahkan event creating untuk mengatur id_so otomatis
        static::creating(function ($orderpenjualan) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel orderpenjualan
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur id_so dengan format "B00(id)"
                $orderpenjualan->id_so = 'SO-000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur id_so dengan format "B00(id)"
                $orderpenjualan->id_so = 'SO-00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur id_so dengan format "B00(id)"
                $orderpenjualan->id_so = 'SO-0' . $nextId . '';
            }
        });
    }

    public function oplines()
    {
        return $this->belongsTo(opline::class);
    }

    public function detailop()
    {
        return $this->hasMany(detail_op::class);
    }
}
