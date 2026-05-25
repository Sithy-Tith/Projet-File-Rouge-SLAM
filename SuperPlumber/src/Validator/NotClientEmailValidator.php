<?php

namespace App\Validator;

use App\Repository\ClientsRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotClientEmailValidator extends ConstraintValidator
{

    // Injecter la dépendance pour le ClientsRepository
    public function __construct(private ClientsRepository $clientsRepository) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        // Vérifier que le champ ne soit pas vide
        if (!$value) {
            return;
        }

        if ($this->clientsRepository->findOneBy(['email' => $value])) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
