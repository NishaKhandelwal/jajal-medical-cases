<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// "Case" is a reserved word in PHP, so the model is called MedicalCase.
class MedicalCase extends Model
{
    use HasFactory;

    protected $table = 'cases';

    public const STATUSES = ['Draft', 'Planning', 'In Review', 'Approved', 'Completed', 'Cancelled'];
    public const PRIORITIES = ['Low', 'Medium', 'High'];
    public const SORTABLE = ['case_number', 'surgeon_name', 'status', 'priority', 'surgery_date', 'created_at'];

    protected $fillable = [
        'case_number',
        'patient_reference',
        'surgeon_name',
        'implant_type',
        'status',
        'priority',
        'surgery_date',
        'created_by',
    ];

    protected $casts = [
        'surgery_date' => 'date:Y-m-d',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Shared search / filter / sort logic used by the web UI and the API. */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $q) use ($search) {
                    $q->where('case_number', 'like', "%{$search}%")
                      ->orWhere('surgeon_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $q, string $v) => $q->where('status', $v))
            ->when($filters['priority'] ?? null, fn (Builder $q, string $v) => $q->where('priority', $v));

        // Whitelist sort column so user input never reaches orderBy directly.
        $sort = in_array($filters['sort'] ?? null, self::SORTABLE, true) ? $filters['sort'] : 'id';
        $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $direction);
    }
}
