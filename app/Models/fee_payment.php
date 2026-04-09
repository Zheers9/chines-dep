<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fee_payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_id',
        'user_id',
        'exam_sub_type_id',
        'register_id',
        'pay',
        'voucher_num',
        'comment',
    ];

    public function register()
    {
        return $this->belongsTo(Register::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function examSubType()
    {
        return $this->belongsTo(examSubType::class);
    }

    public function fee()
    {
        return $this->belongsTo(fee::class);
    }
}
