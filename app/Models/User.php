<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id', '_token'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'kode_perusahaan', 'kode_perusahaan');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Menambahkan event creating untuk mengatur kode_perusahaan otomatis
        static::creating(function ($user) {
            // Menambah 7 jam pada created_at
            // $user->email_verified_at = now()->addHours(7);
            $user->created_at = now()->addHours(7);
            $user->updated_at = now()->addHours(7);
        });
    }
}
