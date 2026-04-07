<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VenueController extends AbstractController
{
    #[Route('/venue', name: 'app_venue')]
    public function index(): Response
    {
        return $this->render('venue/index.html.twig', [
            'controller_name' => 'VenueController',
        ]);
    }
}
