<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

//  Contrainte pour vérifier que l'email de création d'un client n'existe pas dans la table des employés
#[\Attribute]
class NotEmployeeEmail extends Constraint
{
    public string $message = "L'email est déjà utilisé pour un compte Employé. Merci de changer d'email";
}
