<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    protected $appends = ['name']; // Include in JSON output

 

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getNameAttribute()
    {
        $lang = request()->header('Accept-Language') ?? App::getLocale();

        return $lang === 'ar' ? $this->name_ar : $this->name_en;
    }

     public function children()
    {
        return $this->hasMany(Category::class, 'category_id');
    }
}