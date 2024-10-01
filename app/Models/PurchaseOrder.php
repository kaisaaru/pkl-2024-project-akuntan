<?php

namespace App\Models;

use App\Models\po_line;
use App\Models\detail_po;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Barang;

class PurchaseOrder extends Model
{
    use HasFactory;
    // BarangPurchase.php
    protected $hidden = [
        'remember_token',
    ];
    protected $guarded = [
        'id',
        '_token',
    ];
    protected $dates = ['created_at'];

    protected $table = 'purchase_orders';

    public function polines()
    {
        return $this->belongsTo(po_line::class);
    }

    public function detailpo()
    {
        return $this->hasMany(detail_po::class);
    }

    public function perusahaan()
    {
        return $this->hasMany(Perusahaan::class, 'kode_perusahaan', 'kode_perusahaan');
        // return $this->hasMany(BukuBesar::class, 'tipe');
    }
}
