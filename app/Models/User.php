<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'nama_lengkap',
        'role',
        'status',
        'admin_profile_id',
        'guru_profile_id',
        'siswa_profile_id',
    ];

    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class, 'user_id', 'siswa_profile_id');
    }

    public function guruProfile()
    {
        return $this->hasOne(GuruProfile::class, 'user_id', 'guru_profile_id');
    }

    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class, 'id', 'admin_profile_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


}
