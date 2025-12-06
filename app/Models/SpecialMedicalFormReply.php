<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialMedicalFormReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'special_medical_form_id',
        'user_id',
        'note',
        'signature_path',
    ];

    public function specialMedicalForm()
    {
        return $this->belongsTo(SpecialMedicalForm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSignatureUrlAttribute()
    {
        return asset('assets/admin/uploads/' . $this->signature_path);
    }
}