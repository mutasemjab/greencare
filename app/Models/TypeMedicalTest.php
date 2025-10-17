<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeMedicalTest extends Model
{
    use HasFactory;

    protected $guarded = [];

     protected $casts = [
        'price' => 'double',
    ];

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ' . __('messages.currency');
    }

    /**
     * Scope for searching by name
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Scope for price range
     */
    public function scopePriceRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
        
        return $query;
    }
}
