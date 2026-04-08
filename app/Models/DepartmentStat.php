<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'count',
        'label',
        'icon',
    ];
}
