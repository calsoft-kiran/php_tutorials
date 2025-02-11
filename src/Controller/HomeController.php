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
    const ERRORMSG = 'User not authenticated';
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/api/user/me', name: 'api_user_me', methods: ['GET'])]
    public function getLoggedInUser(): JsonResponse
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user) {
            return $this->handleResponse([],Response::HTTP_UNAUTHORIZED,'',self::ERRORMSG);
        } else {
            return $this->handleResponse([
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ],Response::HTTP_OK,'Success','');
        }
    }

    #[Route('/user', name: 'user')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $doctrine->getRepository(User::class)->findall();
        return $this->json($user);
    }

    /***
     * function to handle response of api
     */
    public function handleResponse($data,$status,$message,$error){
        return new JsonResponse(
            [
                'data'=>$data,
                'message' => $message,
                'error'=>$error,
            ],$status );
    }
}
