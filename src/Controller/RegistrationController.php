<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Guide;
use App\Service\JwtService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private JwtService $jwtService
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(\App\Form\RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selectedRole = $form->get('role')->getData();

            // Create the appropriate user type based on selection
            if ($selectedRole === 'GUIDE') {
                $user = new Guide();
            } else {
                $user = new Client();
            }

            // Set user data
            $user->setEmail($data->getEmail());
            $user->setUsername($data->getUsername());
            $user->setRole($selectedRole);
            $user->setRoles([$selectedRole]);

            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Registration successful! You can now login.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
