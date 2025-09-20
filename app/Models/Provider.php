<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function providerCategory()
    {
        return $this->belongsTo(ProviderCategory::class);
    }

}
