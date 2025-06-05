<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $searchEmail = $request->query->get('email');

        $searchResults = null;
        if ($searchEmail) {
            $searchResults = $userRepository->findByEmailLike($searchEmail);
        }

        $allUsers = $userRepository->findAll();

        $userRoles = array_filter($allUsers, function (User $user) {
            return in_array('ROLE_USER', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles());
        });

        $adminRoles = array_filter($allUsers, function (User $user) {
            return in_array('ROLE_ADMIN', $user->getRoles());
        });

        return $this->render('admin_user/index.html.twig', [
            'all_users' => $allUsers,
            'users_only' => $userRoles,
            'admins_only' => $adminRoles,
            'search_results' => $searchResults,
            'search_email' => $searchEmail,
        ]);
    }

    #[Route('/{id}/make-admin', name: 'admin_user_make_admin')]
    public function makeAdmin(User $user, EntityManagerInterface $em): Response
    {
        $roles = $user->getRoles();

        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles(array_unique($roles));
            $em->persist($user);
            $em->flush();
        }

        $this->addFlash('success', 'L’utilisateur a été promu administrateur.');
        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/remove-admin', name: 'admin_user_remove_admin')]
    public function removeAdmin(User $user, EntityManagerInterface $em): Response
    {
        $roles = array_diff($user->getRoles(), ['ROLE_ADMIN']);
        $user->setRoles(array_values($roles));
        $em->persist($user);
        $em->flush();

        $this->addFlash('warning', 'Le rôle administrateur a été retiré à l’utilisateur.');
        return $this->redirectToRoute('admin_user_index');
    }
}
