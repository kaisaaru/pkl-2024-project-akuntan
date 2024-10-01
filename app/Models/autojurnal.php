<?php

namespace App\Models;

use App\Models\SubBukuBesar;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class autojurnal extends Model 
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
    protected $table = 'autojurnals';

    public function polines()
    {
        return $this->belongsTo(SubBukuBesar::class);
    }
}
