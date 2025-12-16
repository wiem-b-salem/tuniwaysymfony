<?php

namespace App\Controller;

use App\Entity\TourPersonnalise;
use App\Entity\User;
use App\Service\TourPersonnaliseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tour/personnalise')]
final class TourPersonnaliseController extends AbstractController
{
    public function __construct(
        private TourPersonnaliseService $tourService,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route(name: 'app_tour_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('tour/index.html.twig', [
            'tours' => $this->tourService->findAll(),
        ]);
    }

    #[Route('/my-tours', name: 'app_tour_my_tours', methods: ['GET'])]
    #[IsGranted('ROLE_GUIDE')]
    public function myTours(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('tour/my_tours.html.twig', [
            'tours' => $user->getTourPersonnalises(),
        ]);
    }

    #[Route('/new', name: 'app_tour_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_GUIDE')]
    public function new(Request $request): Response
    {
        $tour = new TourPersonnalise();
        $form = $this->createForm(\App\Form\TourPersonnaliseType::class, $tour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tour->setGuide($this->getUser());
            $tour->setCreatedAt(new \DateTimeImmutable());

            $this->tourService->create($tour);

            return $this->redirectToRoute('app_tour_my_tours', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tour/new.html.twig', [
            'tour' => $tour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tour_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $tour = $this->tourService->find($id);
        if (!$tour) {
            throw $this->createNotFoundException('Tour not found');
        }

        return $this->render('tour/show.html.twig', [
            'tour' => $tour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tour_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_GUIDE')]
    public function edit(int $id, Request $request): Response
    {
        $tour = $this->tourService->find($id);
        if (!$tour) {
            throw $this->createNotFoundException('Tour not found');
        }

        // Security check: Only owner or admin
        if ($tour->getGuide() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(\App\Form\TourPersonnaliseType::class, $tour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tourService->update($tour);

            return $this->redirectToRoute('app_tour_my_tours', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tour/edit.html.twig', [
            'tour' => $tour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tour_delete', methods: ['POST'])]
    #[IsGranted('ROLE_GUIDE')]
    public function delete(Request $request, int $id): Response
    {
        $tour = $this->tourService->find($id);
        if (!$tour) {
            throw $this->createNotFoundException('Tour not found');
        }

        // Security check
        if ($tour->getGuide() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $tour->getId(), $request->request->get('_token'))) {
            $this->tourService->delete($tour);
        }

        return $this->redirectToRoute('app_tour_my_tours', [], Response::HTTP_SEE_OTHER);
    }
}