<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderCategory extends Model
{
    use HasFactory;
     protected $guarded = [];

     public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function providers()
    {
        return $this->hasMany(Provider::class);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
