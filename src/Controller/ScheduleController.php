<?php

namespace App\Controller;

use App\Repository\ConferenceRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ScheduleController extends AbstractController
{
    #[Route('/schedule', name: 'app_schedule', methods: ['GET'])]
    public function index(ConferenceRepository $conferenceRepository, SessionRepository $sessionRepository): Response
    {
        // Get all upcoming conferences
        $conferences = $conferenceRepository->findBy([], ['startDate' => 'ASC']);

        // Organize sessions by conference and date
        $scheduleData = [];
        
        foreach ($conferences as $conference) {
            $sessions = $sessionRepository->findBy(['conference' => $conference], ['startTime' => 'ASC']);
            
            if (!empty($sessions)) {
                $scheduleData[$conference->getId()] = [
                    'conference' => $conference,
                    'sessions' => $sessions,
                ];
            }
        }

        return $this->render('schedule/index.html.twig', [
            'scheduleData' => $scheduleData,
            'conferences' => $conferences,
        ]);
    }
}
