<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'title',
        'code',
        'stage',
        'semester',
        'description',
        'credits',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
