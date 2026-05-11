<?php

# Généré par IA pour gérer dynamiquement l'accès aux pages suivant le rôle

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\Security\Http\AccessMapInterface;

class AccessControlMatcher
{
    public function __construct(
        private AccessMapInterface $accessMap,
        private Security $security
    ) {}

    public function canAccess(string $path): bool
    {
        $request = Request::create($path);

        [$roles] = $this->accessMap->getPatterns($request);

        // Si aucune règle ne matche → accès public
        if ($roles === null) {
            return true;
        }

        // Vérifie si l'utilisateur a au moins un des rôles requis
        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}
