<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notion extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'name_ku'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
