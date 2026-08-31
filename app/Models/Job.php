<?php

namespace App\Models;

use App\Enums\JobStatus;
use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['customer_id', 'driver_id', 'status', 'scheduled_at', 'completed_at', 'notes'])]
class Job extends Model
{
    /** @use HasFactory<JobFactory> */
    use HasFactory;

    // Table is `delivery_jobs`, not `jobs` — Laravel's database queue driver
    // already owns a `jobs` table for queued jobs.
    protected $table = 'delivery_jobs';

    protected function casts(): array
    {
        return [
            'status' => JobStatus::class,
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<Driver, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
