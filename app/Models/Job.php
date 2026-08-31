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

    // named delivery_jobs — `jobs` is already taken by the queue driver
    protected $table = 'delivery_jobs';

    protected function casts(): array
    {
        return [
            'status' => JobStatus::class,
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // graphql-php's EnumType only matches raw scalars in its value lookup,
    // so backed-enum attributes need unwrapping before they hit the schema.
    public function statusValue(): string
    {
        return $this->status->value;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
