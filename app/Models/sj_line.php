<?php

namespace App\Models;

use App\Models\SuratJalan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class sj_line extends Model
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

    protected $table = 'sj_lines';
    protected static function boot()
    {
        parent::boot();

        // Menambahkan event creating untuk mengatur id_pb otomatis
        static::creating(function ($suratJalan) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel purchaseorder
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur id_pb dengan format "B00(id)"
                $suratJalan->id_sj = 'SJ-000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur id_pb dengan format "B00(id)"
                $suratJalan->id_sj = 'SJ-00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur id_pb dengan format "B00(id)"
                $suratJalan->id_sj = 'SJ-0' . $nextId . '';
            }
        });

        static::creating(function ($detail_sj) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel detail_sj
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur id_detail_sj dengan format "B00(id)"
                $detail_sj->id_detail_sj = 'D.SJ-000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur id_detail_sj dengan format "B00(id)"
                $detail_sj->id_detail_sj = 'D.SJ-00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur id_detail_sj dengan format "B00(id)"
                $detail_sj->id_detail_sj = 'D.SJ-0' . $nextId . '';
            }
        });
    }

    public function suratjalan()
    {
        return $this->hasMany(SuratJalan::class);
    }
}
