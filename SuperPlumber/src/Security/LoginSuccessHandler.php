<?php

namespace App\Security;

use App\Entity\Clients;
use App\Entity\Employees;
use App\Enum\Position;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private RouterInterface $router) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        if ($user instanceof Employees) {
            return match($user->getPosition()) {
                Position::ADMINISTRATOR => new RedirectResponse($this->router->generate('app_employees_index')),
                Position::PLUMBER       => new RedirectResponse($this->router->generate('app_plumber_dashboard')),
                default                 => new RedirectResponse($this->router->generate('app_login')),
            };
        }

        if ($user instanceof Clients) {
            return new RedirectResponse($this->router->generate('app_client_dashboard'));
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }
}
