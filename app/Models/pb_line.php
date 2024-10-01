<?php

namespace App\Models;

use App\Models\PenerimaanBarang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class pb_line extends Model {
    use HasFactory;

    protected $hidden = [
        'remember_token',
    ];
    protected $guarded = [
        'id',
        '_token',
    ];
    protected $dates = ['created_at'];
    protected $fillable = ['user_id','hariini'];

    protected $table = 'pb_lines';
  
    protected static function boot() {
        parent::boot();

        // Menambahkan event creating untuk mengatur id_pb otomatis
        static::creating(function ($penerimaanBarang) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel penerima$penerimaanBarang
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if($nextId < 10) {
                // Mengatur id_pb dengan format "B00(id)"
                $penerimaanBarang->id_pb = 'PB-000'.$nextId.'';
            } else if($nextId < 100) {
                // Mengatur id_pb dengan format "B00(id)"
                $penerimaanBarang->id_pb = 'PB-00'.$nextId.'';
            } else if($nextId < 1000) {
                // Mengatur id_pb dengan format "B00(id)"
                $penerimaanBarang->id_pb = 'PB-0'.$nextId.'';
            }
        });

        static::creating(function ($detail_pb) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel detail_pb
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if($nextId < 10) {
                // Mengatur id_detail_pb dengan format "B00(id)"
                $detail_pb->id_detail_pb = 'D.PB-000'.$nextId.'';
            } else if($nextId < 100) {
                // Mengatur id_detail_pb dengan format "B00(id)"
                $detail_pb->id_detail_pb = 'D.PB-00'.$nextId.'';
            } else if($nextId < 1000) {
                // Mengatur id_detail_pb dengan format "B00(id)"
                $detail_pb->id_detail_pb = 'D.PB-0'.$nextId.'';
            }
        });
    }

    public function penerimaanbarang()
    {
        return $this->hasMany(PenerimaanBarang::class);
    }
}
