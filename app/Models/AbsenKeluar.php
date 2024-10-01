<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AbsenKeluar extends Model
{
    use HasFactory;
    protected $guarded = [
        'log_keluar', '_token'
    ];
    protected $dates = ['created_at'];
}
