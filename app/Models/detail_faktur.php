<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\faktur_beli;

class detail_faktur extends Model
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

    public function fb()
    {
        return $this->belongsTo(faktur_beli::class);
    }

    public function subbukubesar()
    {
        return $this->hasMany(SubBukuBesar::class, 'no_subbukubesar', 'no_bukubesar');
        // return $this->belongsTo(TipeAkun::class, 'tipe');
    }
}
