<?php

namespace App\Exceptions;

use App\Enums\JobStatus;
use GraphQL\Error\ClientAware;
use RuntimeException;

class InvalidJobTransitionException extends RuntimeException implements ClientAware
{
    public static function from(JobStatus $current, JobStatus $target): self
    {
        return new self("Cannot transition job from [{$current->value}] to [{$target->value}].");
    }

    public function isClientSafe(): bool
    {
        return true;
    }
}
