<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class detail_pembayaran extends Model
{
    use HasFactory;
    protected $hidden = [
        'remember_token',
    ];
    protected $guarded = [
        'id', '_token',
    ];
    protected $dates = ['created_at'];
}
