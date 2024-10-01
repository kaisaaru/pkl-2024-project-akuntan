<?php

namespace App\Models;

use App\Models\faktur_line;
use App\Models\detail_faktur;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class faktur_beli extends Model
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

    public function fblines(){
        return $this->belongsTo(faktur_line::class);
    }

    public function detailfb(){
        return $this->hasMany(detail_faktur::class);
    }
}
