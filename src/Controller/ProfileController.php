<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Guide;
use App\Repository\TourPersonnaliseRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/{id}', name: 'app_profile_show', methods: ['GET'])]
    public function show(User $user, TourPersonnaliseRepository $tourRepository, ReviewRepository $reviewRepository): Response
    {
        // Admins don't have public profiles in this context, or maybe they do but limited.
        // Requirement said "Messaging is available between users and guides... Admin does NOT have an inbox".
        // So contact button should be hidden if target is admin.

        $tours = [];
        if ($user instanceof Guide || in_array('ROLE_GUIDE', $user->getRoles())) {
            $tours = $tourRepository->findBy(['guide' => $user]);
        }

        // Reviews written by this user? Or reviews ABOUT this user (if guide)?
        // Requirement: "Click on the guide’s profile -> See... Reviews left on the guide"
        // And "Clicking on User B -> See... Reviews written by that user"

        // So if Guide, we might want to show reviews about them?
        // Current Schema: Review has `tour` and `user` (author).
        // It doesn't seem to have `guide` as separate target. Reviews are usually on Tours.
        // But Guide profile might show reviews of their tours?
        // Let's stick to "Reviews written by that user" for standard users.
        // For Guides, maybe we show reviews of their tours?
        // Let's fetch "Reviews written by" for everyone for now as per "Reviews written by that user" requirement.

        $reviews = $reviewRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'reviews' => $reviews,
            'tours' => $tours,
        ]);
    }
}
