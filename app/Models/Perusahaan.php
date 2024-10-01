<?php

namespace App\Models;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Perusahaan extends Model
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

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kode_perusahaan');
    }


    protected static function boot()
    {
        parent::boot();

        // Menambahkan event creating untuk mengatur kode_perusahaan otomatis
        static::creating(function ($perusahaan) {
            // Menghitung jumlah perusahaan berdasarkan jenis
            $count = self::where('jenis', $perusahaan->jenis)->count();

            // Menghitung id berikutnya
            $nextId = $count + 1;

            // Mengatur kode_perusahaan berdasarkan jenis perusahaan
            if ($perusahaan->jenis == 'Konsumen') {
                $perusahaan->kode_perusahaan = 'K-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            } else if ($perusahaan->jenis == 'Supplier') {
                $perusahaan->kode_perusahaan = 'S-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            } else if ($perusahaan->jenis == 'Developer') {
                $perusahaan->kode_perusahaan = 'D-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }

            // Menambah 7 jam pada created_at
            $perusahaan->created_at = now()->addHours(7);
            $perusahaan->updated_at = now()->addHours(7);
        });
    }
}
