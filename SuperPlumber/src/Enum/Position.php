<?php

namespace App\Enum;

enum Position: string
{
    case ADMINISTRATOR = 'Administrator';
    case PLUMBER = 'Plumber';

    public function label(): string
    {
        return 'position.' . $this->value;
    }
}
