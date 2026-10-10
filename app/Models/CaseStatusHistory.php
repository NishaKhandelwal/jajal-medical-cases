<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseStatusHistory extends Model
{
    protected $fillable = [
        'medical_case_id',
        'old_status',
        'new_status',
        'changed_by',
    ];

    public function medicalCase(): BelongsTo
    {
        return $this->belongsTo(MedicalCase::class, 'medical_case_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}