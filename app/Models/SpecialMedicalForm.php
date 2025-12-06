<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialMedicalForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'created_by',
        'title',
        'note',
        'signature_path',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function replies()
    {
        return $this->hasMany(SpecialMedicalFormReply::class);
    }

    public function getSignatureUrlAttribute()
    {
        return asset('assets/admin/uploads/' . $this->signature_path);
    }
}