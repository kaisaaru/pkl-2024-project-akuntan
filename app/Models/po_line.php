<?php

namespace App\Models;

use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class po_line extends Model
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

    protected $table = 'po_line';
    protected static function boot()
    {
        parent::boot();

        // Menambahkan event creating untuk mengatur id_po otomatis
        static::creating(function ($purchaseorder) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel purchaseorder
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur id_po dengan format "B00(id)"
                $purchaseorder->id_po = 'PO-000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur id_po dengan format "B00(id)"
                $purchaseorder->id_po = 'PO-00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur id_po dengan format "B00(id)"
                $purchaseorder->id_po = 'PO-0' . $nextId . '';
            }
        });

        static::creating(function ($detail_po) {
            $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel detail_po
            $nextId = $lastId + 1; // Menghitung id berikutnya

            if ($nextId < 10) {
                // Mengatur id_detailpo dengan format "B00(id)"
                $detail_po->id_detailpo = 'D.PO-000' . $nextId . '';
            } else if ($nextId < 100) {
                // Mengatur id_detailpo dengan format "B00(id)"
                $detail_po->id_detailpo = 'D.PO-00' . $nextId . '';
            } else if ($nextId < 1000) {
                // Mengatur id_detailpo dengan format "B00(id)"
                $detail_po->id_detailpo = 'D.PO-0' . $nextId . '';
            }
        });
    }

    public function purchaseorder()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
