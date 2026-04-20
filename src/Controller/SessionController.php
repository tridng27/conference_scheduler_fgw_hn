<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\ConferenceRepository;
use App\Repository\SessionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/session')]
final class SessionController extends AbstractController
{
    #[Route('', name: 'app_session_index', methods: ['GET'])]
    public function index(
        Request $request,
        SessionRepository $sessionRepository,
        ConferenceRepository $conferenceRepository,
    ): Response
    {
        $conferenceId = $request->query->getInt('conference', 0);
        $dateValue = $request->query->get('date');
        $dateFilter = null;

        if (is_string($dateValue) && '' !== trim($dateValue)) {
            $dateFilter = \DateTimeImmutable::createFromFormat('Y-m-d', $dateValue) ?: null;
        }

        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findSchedule($conferenceId > 0 ? $conferenceId : null, $dateFilter),
            'conferences' => $conferenceRepository->findBy([], ['startDate' => 'ASC']),
            'selected_conference' => $conferenceId,
            'selected_date' => $dateValue,
        ]);
    }

    #[Route('/new', name: 'app_session_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SessionRepository $sessionRepository,
    ): Response
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conflicts = $this->collectScheduleConflicts($session, $sessionRepository);
            if ([] !== $conflicts) {
                foreach ($conflicts as $message) {
                    $this->addFlash('error', $message);
                }

                return $this->render('session/new.html.twig', [
                    'session' => $session,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($session);
            $entityManager->flush();

            $this->addFlash('success', 'Session created.');

            return $this->redirectToRoute('app_session_index');
        }

        return $this->render('session/new.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_session_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Session $session,
        EntityManagerInterface $entityManager,
        SessionRepository $sessionRepository,
    ): Response
    {
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conflicts = $this->collectScheduleConflicts($session, $sessionRepository);
            if ([] !== $conflicts) {
                foreach ($conflicts as $message) {
                    $this->addFlash('error', $message);
                }

                return $this->render('session/edit.html.twig', [
                    'session' => $session,
                    'form' => $form,
                ]);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Session updated.');

            return $this->redirectToRoute('app_session_index');
        }

        return $this->render('session/edit.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_session_'.$session->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($session);
            $entityManager->flush();
            $this->addFlash('success', 'Session deleted.');
        }

        return $this->redirectToRoute('app_session_index');
    }

    /**
     * @return list<string>
     */
    private function collectScheduleConflicts(Session $session, SessionRepository $sessionRepository): array
    {
        $messages = [];
        $start = $session->getStartTime();
        $end = $session->getEndTime();

        if (null === $start || null === $end) {
            return ['Start and end time are required for schedule validation.'];
        }

        if ($end <= $start) {
            $messages[] = 'End time must be after start time.';
            return $messages;
        }

        $conference = $session->getConference();
        if (null === $conference) {
            $messages[] = 'Conference is required to place a session in the agenda.';
            return $messages;
        }

        $roomIds = $this->collectionIds($session->getRooms());
        $speakerIds = $this->collectionIds($session->getSpeakers());
        $existingSessions = $sessionRepository->findByConferenceForConflict($conference, $session->getId());

        foreach ($existingSessions as $existing) {
            $existingStart = $existing->getStartTime();
            $existingEnd = $existing->getEndTime();
            if (null === $existingStart || null === $existingEnd) {
                continue;
            }

            $overlap = $start < $existingEnd && $end > $existingStart;
            if (!$overlap) {
                continue;
            }

            $sharedRoomIds = array_intersect($roomIds, $this->collectionIds($existing->getRooms()));
            $sharedSpeakerIds = array_intersect($speakerIds, $this->collectionIds($existing->getSpeakers()));

            if ([] !== $sharedRoomIds) {
                $messages[] = sprintf(
                    'Room conflict with "%s" (%s - %s).',
                    $existing->getTitle(),
                    $existingStart->format('Y-m-d H:i'),
                    $existingEnd->format('H:i')
                );
            }
            if ([] !== $sharedSpeakerIds) {
                $messages[] = sprintf(
                    'Speaker conflict with "%s" (%s - %s).',
                    $existing->getTitle(),
                    $existingStart->format('Y-m-d H:i'),
                    $existingEnd->format('H:i')
                );
            }
        }

        return array_values(array_unique($messages));
    }

    /**
     * @return list<int>
     */
    private function collectionIds(Collection $collection): array
    {
        $ids = [];
        foreach ($collection as $item) {
            if (method_exists($item, 'getId') && null !== $item->getId()) {
                $ids[] = $item->getId();
            }
        }

        return $ids;
    }
}
