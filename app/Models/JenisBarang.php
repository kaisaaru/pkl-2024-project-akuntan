<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JenisBarang extends Model
{
    protected $hidden = [
        'remember_token',
    ];
    protected $guarded = [
        'id', '_token', 
    ];
    use HasFactory;
}
