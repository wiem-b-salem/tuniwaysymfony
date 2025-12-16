<?php

namespace App\Controller;

use App\Service\JwtService;
use App\Service\UserService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{

    public function __construct(
        private AuthenticationUtils $authenticationUtils
    ) {
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by logout key on firewall
        // For JWT, logout is typically handled client-side by removing the token
    }
}
