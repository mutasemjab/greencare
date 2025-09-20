<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
     protected $guarded = [];

      public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the template used for this report
     */
    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'report_template_id');
    }

    /**
     * Get the user who created this report
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all answers for this report
     */
    public function answers()
    {
        return $this->hasMany(ReportAnswer::class);
    }

    /**
     * Get answers grouped by section
     */
    public function getAnswersBySection()
    {
        $answers = $this->answers()->with(['field.section'])->get();
        return $answers->groupBy('field.section.id');
    }
}
