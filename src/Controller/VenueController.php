<?php

namespace App\Controller;

use App\Entity\Venue;
use App\Form\VenueType;
use App\Repository\VenueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/venue')]
final class VenueController extends AbstractController
{
    #[Route('', name: 'app_venue_index', methods: ['GET'])]
    public function index(VenueRepository $venueRepository): Response
    {
        return $this->render('venue/index.html.twig', [
            'venues' => $venueRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_venue_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $venue = new Venue();
        $form = $this->createForm(VenueType::class, $venue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($venue);
            $entityManager->flush();

            $this->addFlash('success', 'Venue created.');

            return $this->redirectToRoute('app_venue_index');
        }

        return $this->render('venue/new.html.twig', [
            'venue' => $venue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_venue_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Venue $venue, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VenueType::class, $venue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Venue updated.');

            return $this->redirectToRoute('app_venue_index');
        }

        return $this->render('venue/edit.html.twig', [
            'venue' => $venue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_venue_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Venue $venue, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_venue_'.$venue->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($venue);
            $entityManager->flush();
            $this->addFlash('success', 'Venue deleted.');
        }

        return $this->redirectToRoute('app_venue_index');
    }
}
