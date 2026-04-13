<?php

namespace App\Enum;

enum Type: string
{
    case FUITE = "Fuite d'eau";
    case DEBOUCHAGE = 'Débouchage de canalisation';
    case REPARATION = 'Réparation Chauffe-eau';
    case INSTALLATION = 'Installation robinetterie';
}
