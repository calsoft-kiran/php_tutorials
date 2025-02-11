<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AircraftDetailController extends AbstractController
{
    #[Route('/aircraft/detail', name: 'app_aircraft_detail')]
    public function index(): Response
    {
        return $this->render('aircraft_detail/index.html.twig', [
            'controller_name' => 'AircraftDetailController',
        ]);
    }
}
