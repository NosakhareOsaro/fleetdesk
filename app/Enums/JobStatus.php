<?php

namespace App\Enums;

enum JobStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case InTransit = 'in_transit';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * @return list<JobStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Assigned, self::Cancelled],
            self::Assigned => [self::InTransit, self::Cancelled],
            self::InTransit => [self::Completed, self::Cancelled],
            self::Completed, self::Cancelled => [],
        };
    }

    public function canTransitionTo(JobStatus $status): bool
    {
        return in_array($status, $this->allowedTransitions(), true);
    }
}
