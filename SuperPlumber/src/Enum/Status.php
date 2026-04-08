<?php

namespace App\Enum;

enum Status: string
{
    case TO_PLAN = 'to_plan';
    case PLANNED = 'planned';
    case ONGOING = 'ongoing';
    case FINISHED = 'finished';
    case CANCELED = 'canceled';

}
