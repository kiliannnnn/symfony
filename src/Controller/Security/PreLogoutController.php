<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreLogoutController extends AbstractController
{
    #[Route(path: '/prelogout', name: 'app_prelogout')]
    public function prelogout(Request $request): Response
    {
        $referer = $request->headers->get('referer');

        // If we have a referer, store it in a short-lived, httpOnly cookie so it survives session invalidation
        if ($referer) {
            $response = $this->redirectToRoute('app_logout');
            $cookie = Cookie::create('pre_logout_target', $referer, (new \DateTimeImmutable('+5 minutes')),
                '/', null, false, true, false, Cookie::SAMESITE_LAX);
            $response->headers->setCookie($cookie);
            return $response;
        }

        return $this->redirectToRoute('app_logout');
    }
}
