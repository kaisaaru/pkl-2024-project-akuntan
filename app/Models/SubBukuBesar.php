<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubBukuBesar extends Model
{
    use HasFactory;

    public function bukubesars()
    {
        return $this->hasMany(BukuBesar::class, 'no_bukubesar', 'no_bukubesar');
    }

    protected $hidden = [
        'remember_token',
    ];
    protected $guarded = [
        'id', '_token',
    ];
    protected $dates = ['created_at'];
}
