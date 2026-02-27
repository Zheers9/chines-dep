<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'second_name',
        'third_name',
        'fourth_name',
        'full_name',
        'gender',
        'code_id',
        'birth_date',
        'email',
        'password',
        'notion_id',
        'specialization',
        'occupation',
        'place',
        'nation_code',
    ];

    public function notion()
    {
        return $this->belongsTo(Notion::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'paid_status' => 'boolean',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function registers()
    {
        return $this->hasMany(Register::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function notions()
    {
        return $this->belongsTo(Notion::class, 'notion_id');
    }

    // for search 
    public function scopeSearch($query, $search)
    {
        return $query->where('full_name', 'like', "%{$search}%")
            ->orWhere('first_name', 'like', "%{$search}%")
            ->orWhere('second_name', 'like', "%{$search}%")
            ->orWhere('third_name', 'like', "%{$search}%")
            ->orWhere('fourth_name', 'like', "%{$search}%")
            ->orWhere('nation_code', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('code_id', 'like', "%{$search}%");
    }

    public function scopeFilter($query, $filter)
    {
        if ($filter) {
            $query->where('role_id', $filter)
            ->orWhere('gender', $filter)
            ->orWhere('notion_id', $filter);
        }
        return $query;
    }

}
