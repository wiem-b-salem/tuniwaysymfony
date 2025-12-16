<?php

namespace App\Controller;

use App\Entity\Place;
use App\Service\PlaceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/place')]
final class PlaceController extends AbstractController
{
    public function __construct(private PlaceService $placeService)
    {
    }

    #[Route(name: 'app_place_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('place/index.html.twig', [
            'places' => $this->placeService->findAll(),
        ]);
    }

    #[Route('/admin', name: 'app_place_admin_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(): Response
    {
        return $this->render('place/admin_index.html.twig', [
            'places' => $this->placeService->findAll(),
        ]);
    }

    #[Route('/{id}/favorite', name: 'app_place_favorite', methods: ['POST', 'GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function toggleFavorite(Place $place, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Check if already favorited
        $existingFavori = null;
        foreach ($place->getFavoris() as $favori) {
            if ($favori->getUser() === $user) {
                $existingFavori = $favori;
                break;
            }
        }

        if ($existingFavori) {
            $em->remove($existingFavori);
            $this->addFlash('success', 'Removed from favorites.');
        } else {
            $favori = new \App\Entity\Favori();
            $favori->setUser($user);
            $favori->setPlace($place);
            $favori->setCreatedAt(new \DateTimeImmutable());
            $em->persist($favori);
            $this->addFlash('success', 'Added to favorites!');
        }

        $em->flush();

        return $this->redirectToRoute('app_place_index');
    }

    #[Route('/new', name: 'app_place_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $place = new Place();
        $form = $this->createForm(\App\Form\PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $place->setCreatedAt(new \DateTimeImmutable());
            // You might need to handle file upload for imageUrl here if it's a file input, 
            // but assuming basic text input based on previous JSON or simple form linkage.
            $this->placeService->create($place);

            return $this->redirectToRoute('app_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('place/new.html.twig', [
            'place' => $place,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_place_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $place = $this->placeService->find($id);
        if (!$place) {
            throw $this->createNotFoundException('Place not found');
        }

        return $this->render('place/show.html.twig', [
            'place' => $place,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_place_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(int $id, Request $request): Response
    {
        $place = $this->placeService->find($id);
        if (!$place) {
            throw $this->createNotFoundException('Place not found');
        }

        $form = $this->createForm(\App\Form\PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->placeService->update($place);

            return $this->redirectToRoute('app_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('place/edit.html.twig', [
            'place' => $place,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_place_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, int $id): Response
    {
        $place = $this->placeService->find($id);
        if ($place && $this->isCsrfTokenValid('delete' . $place->getId(), $request->request->get('_token'))) {
            $this->placeService->delete($place);
        }

        return $this->redirectToRoute('app_place_index', [], Response::HTTP_SEE_OTHER);
    }

    private function parsePayload(Request $request): ?array
    {
        try {
            return $request->toArray();
        } catch (\JsonException) {
            return null;
        }
    }
}
