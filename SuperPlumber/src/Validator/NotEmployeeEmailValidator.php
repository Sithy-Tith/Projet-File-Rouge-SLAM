<?php

namespace App\Validator;

use App\Repository\EmployeesRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotEmployeeEmailValidator extends ConstraintValidator
{

    // Injecter la dépendance pour le EmployeesRepository
    public function __construct(private EmployeesRepository $employeesRepository) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        // Vérifier que le champ ne soit pas vide
        if (!$value) {
            return;
        }

        if ($this->employeesRepository->findOneBy(['email' => $value])) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
