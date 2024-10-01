<?php

namespace App\Models;

use App\Models\sj_line;
use App\Models\detail_sj;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SuratJalan extends Model
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

        // Menambahkan event creating untuk mengatur id_sj otomatis
        static::creating(function ($suratjalan) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel suratjalan
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur id_sj dengan format "B00(id)"
                $suratjalan->id_sj = 'SJ-000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur id_sj dengan format "B00(id)"
                $suratjalan->id_sj = 'SJ-00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur id_sj dengan format "B00(id)"
                $suratjalan->id_sj = 'SJ-0' . $nextId . '';
            }
        });
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'nama_perusahaan', 'nama_perusahaan');
    }


    public function sjlines()
    {
        return $this->belongsTo(sj_line::class);
    }

    public function detailsj()
    {
        return $this->hasMany(detail_sj::class);
    }
}