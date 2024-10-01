<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOpname extends Model
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
