<?php

namespace App\Controller;

use App\Entity\Aircraft;
use App\Repository\AircraftRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


final class AircraftController extends AbstractController
{
    #[Route('/api/aircraft/list', name: 'aircraft_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $aircraftRepo = $entityManager->getRepository(Aircraft::class);
        $aircraftList = $aircraftRepo->findAll();
    
        $data = [];
        foreach ($aircraftList as $aircraft) {
            $data[] = [
                'id' => $aircraft->getId(),
                'model' => $aircraft->getModel(),
                'manufacturer' => $aircraft->getManufacturer(),
                'seating_capacity'=>$aircraft->getSeatingCapacity(),
                'max_range'=>$aircraft->getMaxRange(),
                'engine_type'=>$aircraft->getEngineType(),
                'first_flight_date'=>$aircraft->getFirstFlightDate()
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/aircraft/create',name: 'aircraft_create',methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        else {
            $data = json_decode($request->getContent(), true);
            if (!$data || !isset($data['model'], $data['manufacturer'], $data['seatingCapacity'], $data['maxRange'], $data['engineType'], $data['firstFlightDate'])) {
                return $this->json(['error' => 'Invalid input'], 400);
            }
            $aircraft = new Aircraft();
            $aircraft->setModel($data['model']);
            $aircraft->setManufacturer($data['manufacturer']);
            $aircraft->setSeatingCapacity($data['seatingCapacity']);
            $aircraft->setMaxRange($data['maxRange']);
            $aircraft->setEngineType($data['engineType']);
            $aircraft->setFirstFlightDate(new \DateTime($data['firstFlightDate']));
            $entityManager->persist($aircraft);
            $entityManager->flush();
            return $this->json(['message' => 'Aircraft created successfully', 'id' => $aircraft->getId()], 201);
        }
    }


    #[Route('/api/aircraft/find/{id}',name: 'aircraft_find',methods:['get'])]
    public function find(int $id,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        else {
            $aircraft = $entityManager->getRepository(Aircraft::class)->find($id);
            if (!$aircraft) {
                return new JsonResponse(['error' => 'Aircraft not found'], 404);
            }
                return new JsonResponse([
                    'id' => $aircraft->getId(),
                    'model' => $aircraft->getModel(),
                    'manufacturer' => $aircraft->getManufacturer(),
                    'seating_capacity'=>$aircraft->getSeatingCapacity(),
                    'max_range'=>$aircraft->getMaxRange(),
                    'engine_type'=>$aircraft->getEngineType(),
                    'first_flight_date'=>$aircraft->getFirstFlightDate()
                ]);
        }
    }

    #[Route('/api/aircraft/update',name: 'aircraft_update',methods: ['POST'])]
    public function update(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }else {
            return new JsonResponse([
                'id' =>0,
                'status' => 'success',
                'status_code'=>200
            ]);
        }
    }


    #[Route('/api/aircraft/delete/{id}',name: 'aircraft_delete',methods:['delete'])]
    public function delete(int $id,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        else {
            $aircraft = $entityManager->getRepository(Aircraft::class)->find($id);
            if (!$aircraft) {
                return new JsonResponse(['error' => 'Aircraft not found'], 404);
            }
            $entityManager->remove($aircraft);
            $entityManager->flush();
    
            return new JsonResponse(['message' => 'Aircraft deleted successfully'], Response::HTTP_OK);
        }
    }

}
