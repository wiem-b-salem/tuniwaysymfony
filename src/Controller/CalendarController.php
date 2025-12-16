<?php

namespace App\Controller;

use App\Repository\NationalHolidayRepository;
use App\Repository\TourPersonnaliseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/', name: 'app_calendar_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('calendar/index.html.twig');
    }

    #[Route('/events', name: 'app_calendar_events', methods: ['GET'])]
    public function events(
        TourPersonnaliseRepository $tourRepository,
        NationalHolidayRepository $holidayRepository,
        \App\Repository\EventRepository $eventRepository
    ): JsonResponse {
        $events = [];

        // 1. Add Admin-Created Events
        $adminEvents = $eventRepository->findAll();
        foreach ($adminEvents as $event) {
            $events[] = [
                'title' => $event->getName(),
                'start' => $event->getStartDate() ? $event->getStartDate()->format('Y-m-d') : '',
                'end' => $event->getEndDate() ? $event->getEndDate()->format('Y-m-d') : '', // FullCalendar end date is exclusive, might need +1 day if it's all day
                // To keep it simple, we just use the date. If time is present, we can append it.
                // 'start' => $event->getStartDate()->format('Y-m-d') . 'T' . $event->getStartTime()->format('H:i:s'),
                'color' => '#6f42c1', // Purple for Special Events
                'url' => '/admin/event/' . $event->getId() // Link to details? Or just view
            ];
        }

        // 2. Add Holidays
        $holidays = $holidayRepository->findAll();
        foreach ($holidays as $holiday) {
            $events[] = [
                'title' => 'Holiday: ' . $holiday->getName(),
                'start' => $holiday->getDate()->format('Y-m-d'),
                'display' => 'background',
                'color' => '#ff9f89'
            ];
        }

        // 3. Add Tours (Existing Logic)
        $tours = $tourRepository->findAll();
        foreach ($tours as $tour) {
            // Get all approved reservations for this tour
            $approvals = $tour->getReservations()->filter(fn($r) => $r->getStatus() === 'APPROVED');

            $dailyCounts = [];
            foreach ($approvals as $r) {
                $d = $r->getReservationDate()->format('Y-m-d');
                if (!isset($dailyCounts[$d]))
                    $dailyCounts[$d] = 0;
                $dailyCounts[$d] += $r->getNumbersOfPersons();
            }

            foreach ($dailyCounts as $date => $count) {
                if ($count >= $tour->getMaxPersons()) {
                    $events[] = [
                        'title' => 'FULL: ' . $tour->getTitle(),
                        'start' => $date,
                        'color' => '#dc3545',
                        'url' => '/tour/' . $tour->getId()
                    ];
                } else {
                    $events[] = [
                        'title' => 'Tour: ' . $tour->getTitle() . ' (' . ($tour->getMaxPersons() - $count) . ' left)',
                        'start' => $date,
                        'color' => '#28a745',
                        'url' => '/tour/' . $tour->getId()
                    ];
                }
            }
        }

        return $this->json($events);
    }
}
