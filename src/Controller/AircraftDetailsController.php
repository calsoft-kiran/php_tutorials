<?php

namespace App\Controller;

use App\Entity\AircraftDetails;
use App\Form\AircraftDetailsType;
use App\Repository\AircraftDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/aircraft/details')]
final class AircraftDetailsController extends AbstractController
{
    #[Route(name: 'app_aircraft_details_index', methods: ['GET'])]
    public function index(AircraftDetailsRepository $aircraftDetailsRepository): Response
    {
        return $this->render('aircraft_details/index.html.twig', [
            'aircraft_details' => $aircraftDetailsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_aircraft_details_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $aircraftDetail = new AircraftDetails();
        $form = $this->createForm(AircraftDetailsType::class, $aircraftDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($aircraftDetail);
            $entityManager->flush();

            return $this->redirectToRoute('app_aircraft_details_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('aircraft_details/new.html.twig', [
            'aircraft_detail' => $aircraftDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_aircraft_details_show', methods: ['GET'])]
    public function show(AircraftDetails $aircraftDetail): Response
    {
        return $this->render('aircraft_details/show.html.twig', [
            'aircraft_detail' => $aircraftDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_aircraft_details_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AircraftDetails $aircraftDetail, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AircraftDetailsType::class, $aircraftDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_aircraft_details_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('aircraft_details/edit.html.twig', [
            'aircraft_detail' => $aircraftDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_aircraft_details_delete', methods: ['POST'])]
    public function delete(Request $request, AircraftDetails $aircraftDetail, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$aircraftDetail->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($aircraftDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_aircraft_details_index', [], Response::HTTP_SEE_OTHER);
    }
}
