<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'date'=>'date'
    ];

      
    /**
     * Get the user associated with the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function address()
    {
        return $this->belongsTo(UserAddress::class);
    }

    public function orderProducts() 
    {
        return $this->hasMany(OrderProduct::class);
    }
}