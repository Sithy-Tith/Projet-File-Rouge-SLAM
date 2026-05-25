<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

//  Contrainte pour vérifier que l'email de création d'un employé n'existe pas dans la table des clients
#[\Attribute]
class NotClientEmail extends Constraint
{
    public string $message = "L'email est déjà utilisé pour un compte Client. Merci de changer d'email";
}
