<?php

namespace App\Controller;

use App\Entity\Favori;
use App\Entity\Place;
use App\Entity\User;
use App\Service\FavoriService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/favori')]
final class FavoriController extends AbstractController
{
    public function __construct(
        private FavoriService $favoriService,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route(name: 'app_favori_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('favori/index.html.twig', [
            'favoris' => $this->favoriService->findByUser($user),
        ]);
    }

    #[Route('/toggle/{id}', name: 'app_favori_toggle', methods: ['GET', 'POST'])]
    public function toggle(int $id, \App\Service\PlaceService $placeService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $place = $placeService->find($id);
        if (!$place) {
            throw $this->createNotFoundException('Place not found');
        }

        $existing = $this->favoriService->findUserFavoriteForPlace($user, $id);

        if ($existing) {
            $this->favoriService->delete($existing);
            $this->addFlash('success', 'Removed from favorites.');
        } else {
            $favori = new Favori();
            $favori->setUser($user);
            $favori->setPlace($place);
            $this->favoriService->create($favori);
            $this->addFlash('success', 'Added to favorites.');
        }

        // Redirect back to the place page or the favorites list
        // Using HTTP_REFERER if safe, or default to place show
        return $this->redirectToRoute('app_place_show', ['id' => $id]);
    }
}
