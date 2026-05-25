<?php

namespace App\Enum;

enum Status: string
{
    case TO_PLAN = 'to_plan';
    case PLANNED = 'planned';
    case ONGOING = 'ongoing';
    case FINISHED = 'finished';
    case CANCELED = 'canceled';

    // Créer une clé pour Symfony qui sera liée à la value de chaque enum. Ex :
    // status.to_plan -> 'to_plan'
    // Cette clé pourra être utilisée pour proposer des traductions sur ce champs dans /translations/messages.fr.yaml
    public function label(): string
    {
        return 'status.' . $this->value;
    }
}
