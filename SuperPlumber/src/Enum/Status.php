<?php

namespace App\Enum;

enum Status: string
{
    case ONGOING = 'ongoing';
    case TO_PLAN = 'to_plan';
    case FINISHED = 'finished';
    case PLANNED = 'planned';
    case CANCELED = 'canceled';

}
