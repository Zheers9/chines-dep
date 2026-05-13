<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link_url',
        'announcement_id',
        'is_active',
        'order',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;
        return asset('storage/' . $this->image);
    }

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
