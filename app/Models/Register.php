<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Register extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'setting_id',
        'exam_sub_type_id',
        'paid_status',
        'image',
    ];

    protected $casts = [
        'paid_status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }

    public function examSubType()
    {
        return $this->belongsTo(ExamSubType::class);
    }
}