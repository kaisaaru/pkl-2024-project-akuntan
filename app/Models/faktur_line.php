<?php

namespace App\Models;

use App\Models\faktur_beli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class faktur_line extends Model
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
    protected $fillable = ['user_id','hariini'];

    protected $table = 'faktur_lines';
  
    protected static function boot() {
        parent::boot();

        // Menambahkan event creating untuk mengatur id_fb otomatis
        static::creating(function ($faktur_beli) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel penerima$faktur_beli
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if($nextId < 10) {
                // Mengatur id_fb dengan format "B00(id)"
                $faktur_beli->id_fb = 'FB-000'.$nextId.'';
            } else if($nextId < 100) {
                // Mengatur id_fb dengan format "B00(id)"
                $faktur_beli->id_fb = 'FB-00'.$nextId.'';
            } else if($nextId < 1000) {
                // Mengatur id_fb dengan format "B00(id)"
                $faktur_beli->id_fb = 'FB-0'.$nextId.'';
            }
        });

        static::creating(function ($detail_faktur) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel detail_pb
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if($nextId < 10) {
                // Mengatur id_detail_pb dengan format "B00(id)"
                $detail_faktur->id_detailfb = 'D.FB-000'.$nextId.'';
            } else if($nextId < 100) {
                // Mengatur id_detail_pb dengan format "B00(id)"
                $detail_faktur->id_detailfb = 'D.FB-00'.$nextId.'';
            } else if($nextId < 1000) {
                // Mengatur id_detail_pb dengan format "B00(id)"
                $detail_faktur->id_detailfb = 'D.FB-0'.$nextId.'';
            }
        });
    }

    public function fakturbeli()
    {
        return $this->hasMany(faktur_beli::class);
    }
}
