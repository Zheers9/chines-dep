<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fee extends Model
{ 
    use  HasFactory;
    protected $fillable = [
        'setting_id',
        'exam_sub_type_id',
        'user_id',
        'payment_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function examSubType()
    {
        return $this->belongsTo(examSubType::class);
    }

    public function setting()
    {
        return $this->belongsTo(setting::class);
    }
}
