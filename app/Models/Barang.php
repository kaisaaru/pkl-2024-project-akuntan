<?php

namespace App\Models;

use App\Models\Perusahaan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Barang extends Model
{
    use HasFactory;

    // protected $fillable = ['nama_barang', 'jumlah_barang', 'potongan', 'total_bayar'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    protected $hidden = [
        'remember_token',
    ];
    protected $guarded = [
        'id',
        '_token',
    ];
    protected $dates = ['created_at'];
    protected $table = 'barangs';
    protected static function boot()
    {
        parent::boot();

        // Menambahkan event creating untuk mengatur barang_id otomatis
        static::creating(function ($barang) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel Barang
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur barang_id dengan format "B00(id)"
                $barang->barang_id = 'B000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur barang_id dengan format "B00(id)"
                $barang->barang_id = 'B00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur barang_id dengan format "B00(id)"
                $barang->barang_id = 'B0' . $nextId . '';
            }
        });
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'kode_perusahaan');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function detailPo()
    // {
    //     return $this->hasMany(detail_po::class, 'barang_id', 'barang_id');
    // }

    // public function detailPb()
    // {
    //     return $this->hasMany(detail_po::class, 'barang_id', 'barang_id');
    // }

}
