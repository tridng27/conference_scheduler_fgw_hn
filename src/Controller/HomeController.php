<?php

namespace App\Controller;

use App\Repository\ConferenceRepository;
use App\Repository\RegistrationRepository;
use App\Repository\RoomRepository;
use App\Repository\SessionRepository;
use App\Repository\SpeakerRepository;
use App\Repository\UserRepository;
use App\Repository\VenueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/home', name: 'app_home_alias')]
    public function index(
        ConferenceRepository $conferenceRepository,
        SessionRepository $sessionRepository,
        SpeakerRepository $speakerRepository,
        VenueRepository $venueRepository,
        RoomRepository $roomRepository,
        RegistrationRepository $registrationRepository,
        UserRepository $userRepository,
    ): Response
    {
        $todaySessions = $sessionRepository->findTodaySchedule();
        $upcomingSessions = $sessionRepository->findUpcomingSessions(10);

        return $this->render('home/index.html.twig', [
            'counts' => [
                'conferences' => $conferenceRepository->count([]),
                'sessions' => $sessionRepository->count([]),
                'speakers' => $speakerRepository->count([]),
                'venues' => $venueRepository->count([]),
                'rooms' => $roomRepository->count([]),
                'registrations' => $registrationRepository->count([]),
                'users' => $userRepository->count([]),
            ],
            'running_conferences' => $conferenceRepository->findRunningConferences(),
            'upcoming_conferences' => $conferenceRepository->findUpcomingConferences(),
            'today_sessions' => $todaySessions,
            'upcoming_sessions' => $upcomingSessions,
        ]);
    }
}
