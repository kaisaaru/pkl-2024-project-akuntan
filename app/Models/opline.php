<?php

namespace App\Models;

use App\Models\OrderPenjualan;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class opline extends Model
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
    protected $fillable = ['user_id', 'hariini'];

    protected $table = 'oplines';
    protected static function boot()
    {
        parent::boot();

        // Menambahkan event creating untuk mengatur id_po otomatis
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

        static::creating(function ($detail_op) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel detail_op
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur id_detailso dengan format "B00(id)"
                $detail_op->id_detailso = 'D.SO-000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur id_detailso dengan format "B00(id)"
                $detail_op->id_detailso = 'D.SO-00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur id_detailso dengan format "B00(id)"
                $detail_op->id_detailso = 'D.SO-0' . $nextId . '';
            }
        });
    }

    public function orderpenjualan()
    {
        return $this->hasMany(OrderPenjualan::class);
    }
}
