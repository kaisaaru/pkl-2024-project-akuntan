<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class pembayaran extends Model
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

    // protected static function boot() {
    //     parent::boot();

    //     static::creating(function ($payment) {
    //         $lastId = self::max('id'); // Mendapatkan id tertinggi dari tabel penerima$payment
    //         $nextId = $lastId + 1; // Menghitung id berikutnya
    
    //         if($nextId < 10) {
    //             // Mengatur id_pembayaran dengan format "B00(id)"
    //             $payment->id_pembayaran = 'Pay-000'.$nextId.'';
    //         } else if($nextId < 100) {
    //             // Mengatur id_pembayaran dengan format "B00(id)"
    //             $payment->id_pembayaran = 'Pay-00'.$nextId.'';
    //         } else if($nextId < 1000) {
    //             // Mengatur id_pembayaran dengan format "B00(id)"
    //             $payment->id_pembayaran = 'Pay-0'.$nextId.'';
    //         }
    //     });
    // }
    
    protected $table = 'pembayarans';

    
}
