<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'goals',
        'stage_count',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
