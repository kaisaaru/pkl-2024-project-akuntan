<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Carbon;

class Absen extends Model
{
    use HasFactory;

    protected $table = 'tbl_absens';

    protected $guarded = [
        'id', '_token', 
    ];
    protected $dates = ['created_at'];

//     public function setCreatedAtAttribute($value)
// {
//     $createdAt = Carbon::parse($value); // Mengurai tanggal saat ini
//     $this->attributes['created_at'] = $createdAt->addHours(7); // Menambahkan 7 jam
// }
// public function setUpdatedAtAttribute($value)
// {
//     $updatedAt = Carbon::parse($value); // Mengurai tanggal saat ini
//     $this->attributes['updated_at'] = $updatedAt->addHours(7); // Menambahkan 7 jam
// }
}
