<?php

namespace App\Controller;

use App\Repository\ConferenceRepository;
use App\Repository\RegistrationRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        ConferenceRepository $conferenceRepository,
        SessionRepository $sessionRepository,
        RegistrationRepository $registrationRepository,
        UserRepository $userRepository,
    ): Response
    {
        return $this->render('admin/index.html.twig', [
            'stats' => [
                'conferences' => $conferenceRepository->count([]),
                'sessions' => $sessionRepository->count([]),
                'registrations' => $registrationRepository->count([]),
                'users' => $userRepository->count([]),
            ],
        ]);
    }
}
