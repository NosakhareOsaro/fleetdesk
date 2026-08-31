<?php

namespace App\Enums;

enum DriverStatus: string
{
    case Available = 'available';
    case OnJob = 'on_job';
    case OffDuty = 'off_duty';
}
