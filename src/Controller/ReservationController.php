<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\TourPersonnalise;
use App\Repository\ReservationRepository;
use App\Repository\TourPersonnaliseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $this->getUser()->getReservations(),
        ]);
    }

    #[Route('/book/{id}', name: 'app_reservation_book', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function book(int $id, Request $request, TourPersonnaliseRepository $tourRepository): Response
    {
        $tour = $tourRepository->find($id);
        if (!$tour) {
            throw $this->createNotFoundException('Tour not found');
        }

        $dateStr = $request->request->get('date');
        $persons = (int) $request->request->get('persons', 1);

        if (!$dateStr) {
            $this->addFlash('error', 'Please select a date.');
            return $this->redirectToRoute('app_tour_show', ['id' => $id]);
        }

        try {
            $date = new \DateTime($dateStr);
        } catch (\Exception) {
            $this->addFlash('error', 'Invalid date.');
            return $this->redirectToRoute('app_tour_show', ['id' => $id]);
        }

        // Check Capacity
        $currentReservations = $tour->getReservations()->filter(function (Reservation $r) use ($date) {
            return $r->getStatus() !== 'REJECTED' && $r->getReservationDate()->format('Y-m-d') === $date->format('Y-m-d');
        });

        $currentCount = 0;
        foreach ($currentReservations as $r) {
            $currentCount += $r->getNumbersOfPersons();
        }

        if ($currentCount + $persons > $tour->getMaxPersons()) {
            $this->addFlash('error', 'Not enough spots available for this date. Remaining: ' . ($tour->getMaxPersons() - $currentCount));
            return $this->redirectToRoute('app_tour_show', ['id' => $id]);
        }

        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setTour($tour);
        $reservation->setReservationDate($date);
        $reservation->setNumbersOfPersons($persons);
        $reservation->setStatus('PENDING'); // Default
        $reservation->setCreatedAt(new \DateTime());
        $reservation->setType('TOUR');
        $reservation->setTotalPrice($tour->getPrice() * $persons);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        $this->addFlash('success', 'Reservation request sent!');
        return $this->redirectToRoute('app_reservation_index');
    }

    #[Route('/manage', name: 'app_reservation_manage', methods: ['GET'])]
    #[IsGranted('ROLE_GUIDE')]
    public function manage(ReservationRepository $reservationRepository): Response
    {
        // Find reservations for tours owned by this guide
        // Using a custom query or filtering in memory (inefficient but works for now)
        // Ideally: $reservationRepository->findByGuide($this->getUser());

        // For MVP, letting Doctrine handling through Tour relation if possible, or manual filter
        $guide = $this->getUser();
        $tours = $guide->getTourPersonnalises();
        $reservations = [];

        foreach ($tours as $tour) {
            foreach ($tour->getReservations() as $res) {
                $reservations[] = $res;
            }
        }

        // Sort by date desc
        usort($reservations, fn($a, $b) => $b->getCreatedAt() <=> $a->getCreatedAt());

        return $this->render('reservation/manage.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/approve/{id}', name: 'app_reservation_approve', methods: ['POST'])]
    #[IsGranted('ROLE_GUIDE')]
    public function approve(Reservation $reservation, Request $request): Response
    {
        // Security check: guide owns tour
        if ($reservation->getTour()->getGuide() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('approve' . $reservation->getId(), $request->request->get('_token'))) {
            $reservation->setStatus('APPROVED');
            $this->entityManager->flush();
            $this->addFlash('success', 'Reservation approved.');
        }

        return $this->redirectToRoute('app_reservation_manage');
    }

    #[Route('/reject/{id}', name: 'app_reservation_reject', methods: ['POST'])]
    #[IsGranted('ROLE_GUIDE')]
    public function reject(Reservation $reservation, Request $request): Response
    {
        if ($reservation->getTour()->getGuide() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('reject' . $reservation->getId(), $request->request->get('_token'))) {
            $reservation->setStatus('REJECTED');
            $this->entityManager->flush();
            $this->addFlash('success', 'Reservation rejected.');
        }

        return $this->redirectToRoute('app_reservation_manage');
    }
}
