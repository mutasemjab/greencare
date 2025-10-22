<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TypeHomeXray extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'price' => 'double',
    ];

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(TypeHomeXray::class, 'parent_id');
    }

    /**
     * Get the child subcategories
     */
    public function children()
    {
        return $this->hasMany(TypeHomeXray::class, 'parent_id');
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Check if this is a parent category (has no parent)
     */
    public function IsMainCategory()
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this is a subcategory (has a parent)
     */
    public function isSubcategory()
    {
        return !is_null($this->parent_id);
    }

    /**
     * Get the full category path (Parent > Child)
     */
    public function getCategoryPathAttribute()
    {
        if ($this->IsMainCategory()) {
            return $this->name;
        }
        
        return $this->parent->name . ' > ' . $this->name;
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ' . __('messages.currency');
    }

    /**
     * Scope for parent categories only
     */
    public function scopeParentsOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for subcategories only
     */
    public function scopeSubcategoriesOnly($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope for children of specific parent
     */
    public function scopeChildrenOf($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
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

    /**
     * Get hierarchical list for dropdowns
     */
    public static function getHierarchicalList()
    {
        $categories = [];
        $parents = self::parentsOnly()->orderBy('name')->get();
        
        foreach ($parents as $parent) {
            $categories[$parent->id] = $parent->name;
            
            $children = $parent->children()->orderBy('name')->get();
            foreach ($children as $child) {
                $categories[$child->id] = '-- ' . $child->name;
            }
        }
        
        return $categories;
    }

    /**
     * Boot method to handle cascading deletes
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($category) {
            // When deleting a parent, move children to root level or handle as needed
            if ($category->IsMainCategory()) {
                $category->children()->update(['parent_id' => null]);
            }
        });
    }
}
