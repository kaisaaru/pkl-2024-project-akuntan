<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Neraca extends Model
{
    use HasFactory;

    protected $fillable = [
        'neraca',
        'jumlah',  // Add this line
        // other attributes...
    ];
}
