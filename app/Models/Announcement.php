<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'main_image', 'type', 'event_date'];
    
    protected $appends = ['main_image_url'];

    public function images()
    {
        return $this->hasMany(AnnouncementImage::class, 'announcement_id');
    }

    public function getMainImageUrlAttribute()
    {
        return $this->main_image ? asset('storage/' . $this->main_image) : null;
    }
}

class AnnouncementImage extends Model
{
    protected $fillable = ['announcement_id', 'image_path'];
    
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
