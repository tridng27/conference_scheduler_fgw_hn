<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    #[Route('', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findBy([], ['createdAt' => 'DESC']),
            'admin_count' => $userRepository->countAdmins(),
        ]);
    }

    #[Route('/{id}/promote', name: 'app_user_promote', methods: ['POST'])]
    public function promote(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
    ): Response {
        if (!$this->isCsrfTokenValid('promote_user_'.$user->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_user_index');
        }

        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles, true)) {
            $roles[] = 'ROLE_ADMIN';
            $roles[] = 'ROLE_USER';
            $user->setRoles(array_values(array_unique($roles)));
            $user->setRole('ADMIN');
            $entityManager->flush();
            $this->addFlash('success', sprintf('%s promoted to admin.', $user->getEmail()));
        } else {
            $this->addFlash('error', 'User is already an admin.');
        }

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/{id}/demote', name: 'app_user_demote', methods: ['POST'])]
    public function demote(
        Request $request,
        User $user,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        if (!$this->isCsrfTokenValid('demote_user_'.$user->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_user_index');
        }

        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles, true)) {
            $this->addFlash('error', 'User is not an admin.');

            return $this->redirectToRoute('app_user_index');
        }

        $current = $this->getUser();
        $isSelf = $current instanceof User && $current->getId() === $user->getId();
        $adminCount = $userRepository->countAdmins();
        if ($adminCount <= 1) {
            $this->addFlash('error', $isSelf ? 'You cannot demote yourself as the last admin.' : 'At least one admin account must remain.');

            return $this->redirectToRoute('app_user_index');
        }

        $user->setRoles(['ROLE_USER']);
        $user->setRole('USER');
        $entityManager->flush();
        $this->addFlash('success', sprintf('%s demoted to user.', $user->getEmail()));

        return $this->redirectToRoute('app_user_index');
    }
}
