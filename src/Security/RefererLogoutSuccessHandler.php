<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RefererLogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onLogoutSuccess(Request $request)
    {
        $referer = $request->headers->get('referer');
        if ($referer) {
            return new RedirectResponse($referer);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}
