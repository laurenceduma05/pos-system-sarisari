<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Enum as RulesEnum;

final class RoleType extends Enum
{
    const ADMIN = 1;
    const USER = 2;
}

