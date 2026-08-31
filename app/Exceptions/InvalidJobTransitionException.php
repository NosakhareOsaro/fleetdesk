<?php

namespace App\Exceptions;

use App\Enums\JobStatus;
use RuntimeException;

class InvalidJobTransitionException extends RuntimeException
{
    public static function from(JobStatus $current, JobStatus $target): self
    {
        return new self("Cannot transition job from [{$current->value}] to [{$target->value}].");
    }
}
