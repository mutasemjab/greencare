<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
     protected $guarded = [];

     protected $casts = [
        'date_of_news' => 'date',
    ];

    // Get title based on current locale
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    // Get description based on current locale
    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    // Get formatted date
    public function getFormattedDateAttribute()
    {
        return $this->date_of_news->format('d M Y');
    }
}
