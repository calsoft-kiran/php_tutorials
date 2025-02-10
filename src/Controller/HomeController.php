<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class HomeController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/api/user/me', name: 'api_user_me', methods: ['GET'])]
    public function getLoggedInUser(): JsonResponse
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail()
        ]);
    }

    #[Route('/user', name: 'user')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $doctrine->getRepository(User::class)->findall();
        return $this->json($user);
    }
}
