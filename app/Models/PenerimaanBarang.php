<?php

namespace App\Models;

use App\Models\pb_line;
use App\Models\detail_pb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PenerimaanBarang extends Model
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

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'nama_perusahaan');
    }
    
    public function pblines()
    {
        return $this->belongsTo(pb_line::class);
    }

    public function detailpb()
    {
        return $this->hasMany(detail_pb::class);
    }
}