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
        'is_accepted',
        'image',
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(fee_payment::class, 'register_id');
    }

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