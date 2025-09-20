<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['name', 'description', 'is_favourite'];

    protected $casts = [
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'price_after_discount' => 'decimal:2',
    ];



    public function getNameAttribute()
    {
        $lang = request()->header('Accept-Language') ?? App::getLocale();
        return $lang === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getDescriptionAttribute()
    {
        $lang = request()->header('Accept-Language') ?? App::getLocale();
        return $lang === 'ar' ? $this->description_ar : $this->description_en;
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

   
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

   

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }


    public function getIsFavouriteAttribute()
    {
        if (!auth()->check()) {
            return 0;
        }

        return DB::table('product_favourites')
            ->where('product_id', $this->id)
            ->where('user_id', auth()->id())
            ->exists() ? 1 : 0;
    }

    // Add the relationship in Product model if not already exists
    public function favouritedBy()
    {
        return $this->belongsToMany(User::class, 'product_favourites', 'product_id', 'user_id');
    }
}
