<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

final class AircraftDetailController extends AbstractController
{
    private $customLogger;

    public function __construct(LoggerInterface $customLogger)
    {
        $this->customLogger = $customLogger;
    }

    #[Route('/aircraft/detail', name: 'app_aircraft_detail')]
    public function index(): Response
    {
        return $this->render('aircraft_detail/index.html.twig', [
            'controller_name' => 'AircraftDetailController',
        ]);
    }

    
    #[Route('/add/new/user', name: 'add_user',methods:['POST'])]
    public function addNewUser(Request $request): Response
    {
        $this->customLogger->info('This is an INFO log from controller');
        return new Response('Logs have been written. Check var/log/dev.log');
    }
}
