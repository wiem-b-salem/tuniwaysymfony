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
    public function events(TourPersonnaliseRepository $tourRepository, NationalHolidayRepository $holidayRepository): JsonResponse
    {
        $events = [];

        // Add Holidays
        $holidays = $holidayRepository->findAll();
        foreach ($holidays as $holiday) {
            $events[] = [
                'title' => 'Holiday: ' . $holiday->getName(),
                'start' => $holiday->getDate()->format('Y-m-d'),
                'display' => 'background', // Or specific color
                'color' => '#ff9f89' // Light red
            ];
        }

        // Add Tours (Assuming they occur on available dates? Or just show all distinct booked dates?
        // Requirement said: "Tours scheduled on specific dates"
        // But Tour entity doesn't have a recurring schedule/date entity.
        // It seems reservations define the dates. Or Tours are just 'offerings'.
        // But "Capacity management per date" implies Tours have specific dates or are available every day.
        // I will assume for now we show DATES where Reservations exist (i.e. Scheduled Tours) 
        // OR better: The requirement says later "Tours scheduled on specific dates". 
        // For now, I will display "Available Tours" if I knew their schedule.
        // Given current schema, a Tour is an offering without specific dates until booked.
        // I will display date-based reservations for "My Tours" if user is guide?
        // Or "Shared Tours" implying public view?
        // "Add a calendar view where users can see: Tours scheduled on specific dates".
        // This implies there should be a `TourDate` or `TourSchedule` entity. 
        // Since I don't have that, I'll list existing Reservations as "Confirmed Tours".
        // AND maybe just assume Tours are available daily?
        // Let's use existing APPROVED reservations as "Scheduled Tours".

        // BETTER APPROACH for "Users can see Tours scheduled":
        // Show APPROVED reservations as "Confirmed Trips" that others can maybe join? 
        // Or just show Holidays. 
        // I will query Confirmed Reservations. Each reservation represents a booked tour instance.

        /* 
           Simpler Interpretation: 
           Just show Holidays for now + maybe arbitrary tour availability if asked.
           But to meet "Tours scheduled", I will fetch APPROVED reservations.
        */

        /* 
           Wait, "When the maximum number of participants is reached for a specific date".
           This implies ANY date is valid until full.
           So I should probably show "Full" dates.
           I will iterate through all APPROVED reservations, group by Tour & Date, check sum(persons) >= maxPersons.
           If Full, mark as red event "Full: Tour Name".
        */

        $tours = $tourRepository->findAll();
        // This acts as a global check. Realistically this is heavy computation for controller.

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
                        'color' => '#dc3545', // Red
                        'url' => '/tour/' . $tour->getId()
                    ];
                } else {
                    // Optionally show available tours?
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
