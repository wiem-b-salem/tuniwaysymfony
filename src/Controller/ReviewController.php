<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use App\Service\PlaceService;
use App\Service\TourPersonnaliseService;
use App\Service\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/review')]
class ReviewController extends AbstractController
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/admin', name: 'app_review_admin_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(): Response
    {
        return $this->render('admin/review/index.html.twig', [
            'reviews' => $this->reviewRepository->findBy(['status' => 'PENDING'], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/form/{type}/{id}', name: 'app_review_form', methods: ['GET', 'POST'])]
    public function reviewForm(string $type, int $id, Request $request, PlaceService $placeService, TourPersonnaliseService $tourService): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review, [
            'action' => $this->generateUrl('app_review_form', ['type' => $type, 'id' => $id]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                return $this->redirectToRoute('app_login');
            }

            $review->setUser($this->getUser());
            $review->setCreatedAt(new \DateTimeImmutable());
            $review->setStatus('PENDING');

            if ($type === 'place') {
                $place = $placeService->find($id);
                if (!$place)
                    throw $this->createNotFoundException();
                $review->setPlace($place);
                $redirectRoute = 'app_place_show';
            } else {
                $tour = $tourService->find($id);
                if (!$tour)
                    throw $this->createNotFoundException();
                $review->setTour($tour);
                $redirectRoute = 'app_tour_show';
            }

            $this->entityManager->persist($review);
            $this->entityManager->flush();

            $this->addFlash('success', 'Review submitted! It will be visible after moderation.');
            return $this->redirectToRoute($redirectRoute, ['id' => $id]);
        }

        return $this->render('review/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/approve/{id}', name: 'app_review_approve', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Review $review, Request $request): Response
    {
        if ($this->isCsrfTokenValid('approve' . $review->getId(), $request->request->get('_token'))) {
            $review->setStatus('APPROVED');
            $this->entityManager->flush();
            $this->addFlash('success', 'Review approved.');
        }

        return $this->redirectToRoute('app_review_admin_index');
    }

    #[Route('/admin/delete/{id}', name: 'app_review_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Review $review, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($review);
            $this->entityManager->flush();
            $this->addFlash('success', 'Review deleted.');
        }

        return $this->redirectToRoute('app_review_admin_index');
    }
}