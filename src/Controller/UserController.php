<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
final class UserController extends AbstractController
{
    private const SYSTEM_ADMIN_EMAIL = 'Admin@gmail.com';

    #[Route('', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $user = new User();
        $user->setCreatedAt(new \DateTime());

        $form = $this->createForm(UserType::class, $user, [
            'require_password' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (0 !== strcasecmp((string) $user->getEmail(), self::SYSTEM_ADMIN_EMAIL) && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                $user->setRoles(['ROLE_USER']);
                $user->setRole('USER');
                $this->addFlash('error', 'Only the system admin account can have ROLE_ADMIN.');
            }

            $plainPassword = (string) $form->get('plainPassword')->getData();
            if ('' === $plainPassword) {
                $this->addFlash('error', 'Password is required for new users.');

                return $this->render('user/new.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }

            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User created.');

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $form = $this->createForm(UserType::class, $user, [
            'require_password' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (0 !== strcasecmp((string) $user->getEmail(), self::SYSTEM_ADMIN_EMAIL) && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                $user->setRoles(['ROLE_USER']);
                $user->setRole('USER');
                $this->addFlash('error', 'Only the system admin account can have ROLE_ADMIN.');
            }

            if (0 === strcasecmp((string) $user->getEmail(), self::SYSTEM_ADMIN_EMAIL)) {
                $user->setRoles(['ROLE_ADMIN']);
                $user->setRole('ADMIN');
            }

            $plainPassword = (string) $form->get('plainPassword')->getData();
            if ('' !== $plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->flush();
            $this->addFlash('success', 'User updated.');

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (0 === strcasecmp((string) $user->getEmail(), self::SYSTEM_ADMIN_EMAIL)) {
            $this->addFlash('error', 'The system admin account cannot be deleted.');

            return $this->redirectToRoute('app_user_index');
        }

        if ($this->isCsrfTokenValid('delete_user_'.$user->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User deleted.');
        }

        return $this->redirectToRoute('app_user_index');
    }
}
