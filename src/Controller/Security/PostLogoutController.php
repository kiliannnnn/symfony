<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostLogoutController extends AbstractController
{
    #[Route(path: '/logout-after', name: 'app_logout_after')]
    public function after(Request $request): Response
    {
        $target = $request->cookies->get('pre_logout_target');

        $response = null;
        if ($target) {
            // clear the cookie by setting it expired
            $response = $this->redirect($target);
            $response->headers->clearCookie('pre_logout_target');
            return $response;
        }

    return $this->redirectToRoute('home');
    }
}
