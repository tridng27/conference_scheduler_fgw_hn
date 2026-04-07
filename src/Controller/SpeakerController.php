<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SpeakerController extends AbstractController
{
    #[Route('/speaker', name: 'app_speaker')]
    public function index(): Response
    {
        return $this->render('speaker/index.html.twig', [
            'controller_name' => 'SpeakerController',
        ]);
    }
}
