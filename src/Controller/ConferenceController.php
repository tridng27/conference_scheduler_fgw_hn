<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/conference')]
final class ConferenceController extends AbstractController
{
    #[Route('', name: 'app_conference_index', methods: ['GET'])]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return $this->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findBy([], ['startDate' => 'ASC']),
        ]);
    }

    #[Route('/{id}/show', name: 'app_conference_show', methods: ['GET'])]
    public function show(Conference $conference): Response
    {
        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
        ]);
    }

    #[Route('/new', name: 'app_conference_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $conference = new Conference();
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTime();
            if (null === $conference->getCreateAt()) {
                $conference->setCreateAt(clone $now);
            }
            if (null === $conference->getCreatedAt()) {
                $conference->setCreatedAt(clone $now);
            }

            $entityManager->persist($conference);
            $entityManager->flush();

            $this->addFlash('success', 'Conference created.');

            return $this->redirectToRoute('app_conference_index');
        }

        return $this->render('conference/new.html.twig', [
            'conference' => $conference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_conference_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Conference $conference, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $conference->getCreateAt()) {
                $conference->setCreateAt(new \DateTime());
            }

            $entityManager->flush();
            $this->addFlash('success', 'Conference updated.');

            return $this->redirectToRoute('app_conference_index');
        }

        return $this->render('conference/edit.html.twig', [
            'conference' => $conference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_conference_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Conference $conference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_conference_'.$conference->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($conference);
            $entityManager->flush();
            $this->addFlash('success', 'Conference deleted.');
        }

        return $this->redirectToRoute('app_conference_index');
    }
}
