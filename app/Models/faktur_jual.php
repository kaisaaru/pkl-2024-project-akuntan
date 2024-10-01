<?php

namespace App\Models;

use App\Models\fj_line;
use App\Models\detail_fj;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class faktur_jual extends Model
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

    public function fjlines(){
        return $this->belongsTo(fj_line::class);
    }

    public function detailfj(){
        return $this->hasMany(detail_fj::class);
    }
}
