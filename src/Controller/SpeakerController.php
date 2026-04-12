<?php

namespace App\Controller;

use App\Entity\Speaker;
use App\Form\SpeakerType;
use App\Repository\SpeakerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/speaker')]
final class SpeakerController extends AbstractController
{
    #[Route('', name: 'app_speaker_index', methods: ['GET'])]
    public function index(SpeakerRepository $speakerRepository): Response
    {
        return $this->render('speaker/index.html.twig', [
            'speakers' => $speakerRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_speaker_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $speaker = new Speaker();
        $form = $this->createForm(SpeakerType::class, $speaker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($speaker);
            $entityManager->flush();

            $this->addFlash('success', 'Speaker created.');

            return $this->redirectToRoute('app_speaker_index');
        }

        return $this->render('speaker/new.html.twig', [
            'speaker' => $speaker,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_speaker_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Speaker $speaker, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SpeakerType::class, $speaker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Speaker updated.');

            return $this->redirectToRoute('app_speaker_index');
        }

        return $this->render('speaker/edit.html.twig', [
            'speaker' => $speaker,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_speaker_delete', methods: ['POST'])]
    public function delete(Request $request, Speaker $speaker, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_speaker_'.$speaker->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($speaker);
            $entityManager->flush();
            $this->addFlash('success', 'Speaker deleted.');
        }

        return $this->redirectToRoute('app_speaker_index');
    }
}
