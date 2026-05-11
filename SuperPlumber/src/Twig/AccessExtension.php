<?php

# Généré par IA pour gérer dynamiquement l'accès aux pages suivant le rôle

namespace App\Twig;

use App\Security\AccessControlMatcher;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AccessExtension extends AbstractExtension
{
    public function __construct(private AccessControlMatcher $matcher) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('can_access_path', [$this->matcher, 'canAccess']),
        ];
    }
}
