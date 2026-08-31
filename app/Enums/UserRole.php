<?php

namespace App\Enums;

enum UserRole: string
{
    case Dispatcher = 'dispatcher';
    case Driver = 'driver';
    case Manager = 'manager';
}
