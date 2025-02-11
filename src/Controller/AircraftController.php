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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Exception;

final class AircraftController extends AbstractController
{
    const ERRORMSG = 'User not authenticated';
    const ERRORMSGTWO = 'Aircraft details not found';

    #[Route('/api/aircraft/list', name: 'aircraft_list')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        try
        {
            $user = $this->getUser();

            if (!$user) {
                return $this->handleResponse([],Response::HTTP_UNAUTHORIZED,'',self::ERRORMSG);
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
            return $this->handleResponse($data,Response::HTTP_OK,'Success','');
        }catch(Exception $e){
            return $this->handleResponse([],Response::HTTP_INTERNAL_SERVER_ERROR,'',$e->getMessage());
        }
    }

    #[Route('/api/aircraft/create',name: 'aircraft_create',methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager,ValidatorInterface $validator): JsonResponse
    {
        try
        {
            $response='';
            $user = $this->getUser();
            if (!$user) {
                $response = $this->handleResponse([],Response::HTTP_UNAUTHORIZED,'',self::ERRORMSG);
            }
            else {
                $data = json_decode($request->getContent(), true);
                if (!$data || !isset($data['model'], $data['manufacturer'], $data['seatingCapacity'], $data['maxRange'], $data['engineType'], $data['firstFlightDate'])) {
                    $response = $this->handleResponse([],Response::HTTP_BAD_REQUEST,'','Invalid input');
                }
                else {

                    $constraints = new Assert\Collection([
                        'model' => [new Assert\NotBlank(['message' => 'Model is required.']), new Assert\Length(['min' => 3, 'max' => 100])],
                        'manufacturer' => [new Assert\NotBlank(['message' => 'Manufacturer is required.']), new Assert\Length(['min' => 3, 'max' => 100])],
                        'seatingCapacity' => [new Assert\NotBlank(['message' => 'Seating capacity is required.']), new Assert\Positive()],
                        'maxRange' => [new Assert\NotBlank(['message' => 'Max range is required.']), new Assert\Positive()],
                        'engineType' => [new Assert\NotBlank(['message' => 'Engine type is required.']), new Assert\Length(['min' => 3, 'max' => 100])],
                        'firstFlightDate' => [new Assert\NotBlank(['message' => 'First flight date is required.']), new Assert\Date()],
                    ]);
            
                    $errors = $validator->validate($data, $constraints);
            
                    if (count($errors) > 0) {
                        $errorMessages = [];
                        foreach ($errors as $error) {
                            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                        }
                        $response = $this->handleResponse([],Response::HTTP_BAD_REQUEST,'',$errorMessages);
                    }
                    else {
                        $aircraft = new Aircraft();
                        $aircraft->setModel($data['model']);
                        $aircraft->setManufacturer($data['manufacturer']);
                        $aircraft->setSeatingCapacity($data['seatingCapacity']);
                        $aircraft->setMaxRange($data['maxRange']);
                        $aircraft->setEngineType($data['engineType']);
                        $aircraft->setFirstFlightDate(new \DateTime($data['firstFlightDate']));
                        $entityManager->persist($aircraft);
                        $entityManager->flush();
                        $response = $this->handleResponse(['id' => $aircraft->getId()],Response::HTTP_OK,'Aircraft created successfully','');
                    }
                }
            }
        }catch(Exception $e){
            return $this->handleResponse([],Response::HTTP_INTERNAL_SERVER_ERROR,'',$e->getMessage());
        }
        return $response;
    }


    #[Route('/api/aircraft/find/{id}',name: 'aircraft_find',methods:['get'])]
    public function find(int $id,EntityManagerInterface $entityManager): JsonResponse
    {
        try
        {
            $response='';
            $user = $this->getUser();
            if (!$user) {
                $response = $this->handleResponse([],Response::HTTP_UNAUTHORIZED,'',self::ERRORMSG);
            }
            else {
                $aircraft = $entityManager->getRepository(Aircraft::class)->find($id);
                if (!$aircraft) {
                    $response = $this->handleResponse([],Response::HTTP_NOT_FOUND,'',self::ERRORMSGTWO);
                }
                else {
                    $data = [
                        'id' => $aircraft->getId(),
                        'model' => $aircraft->getModel(),
                        'manufacturer' => $aircraft->getManufacturer(),
                        'seating_capacity'=>$aircraft->getSeatingCapacity(),
                        'max_range'=>$aircraft->getMaxRange(),
                        'engine_type'=>$aircraft->getEngineType(),
                        'first_flight_date'=>$aircraft->getFirstFlightDate()
                    ];
                    $response = $this->handleResponse($data,Response::HTTP_OK,'success','');
                }
            }
        }catch(Exception $e){
            return $this->handleResponse([],Response::HTTP_INTERNAL_SERVER_ERROR,'',$e->getMessage());
        }
        return $response;
    }

    #[Route('/api/aircraft/update/{id}',name: 'aircraft_update',methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try
        {
            $response='';
            $user = $this->getUser();
            if (!$user) {
                $response = $this->handleResponse([],Response::HTTP_UNAUTHORIZED,'',self::ERRORMSG);
            } else {
                $aircraft = $entityManager->getRepository(Aircraft::class)->find($id);

                if (!$aircraft) {
                    $response = $this->handleResponse([],Response::HTTP_NOT_FOUND,'',self::ERRORMSGTWO);
                }
                else {
                    $data = json_decode($request->getContent(), true);

                    if (isset($data['model'])) {
                        $aircraft->setModel($data['model']);
                    }

                    if (isset($data['manufacturer'])) {
                        $aircraft->setManufacturer($data['manufacturer']);
                    }

                    if (isset($data['max_range'])) {
                        $aircraft->setMaxRange($data['max_range']);
                    }

                    if (isset($data['engine_type'])) {
                        $aircraft->setEngineType($data['engine_type']);
                    }

                    if (isset($data['seatingCapacity'])) {
                        $aircraft->setSeatingCapacity($data['seatingCapacity']);
                    }

                    if (isset($data['firstFlightDate'])) {
                        $aircraft->setFirstFlightDate(new \DateTime($data['firstFlightDate']));
                    }
                    $entityManager->flush();
                    $response = $this->handleResponse([
                        'model' => $aircraft->getModel(),
                            'manufacturer' => $aircraft->getManufacturer(),
                            'seating_capacity'=>$aircraft->getSeatingCapacity(),
                            'max_range'=>$aircraft->getMaxRange(),
                            'engine_type'=>$aircraft->getEngineType(),
                            'first_flight_date'=>$aircraft->getFirstFlightDate()
                    ],Response::HTTP_OK,'Aircraft updated successfully','');
                }

            }
        }catch(Exception $e){
            return $this->handleResponse([],Response::HTTP_INTERNAL_SERVER_ERROR,'',$e->getMessage());
        }
        return $response;
    }


    #[Route('/api/aircraft/delete/{id}',name: 'aircraft_delete',methods:['delete'])]
    public function delete(int $id,EntityManagerInterface $entityManager): JsonResponse
    {
        try
        {
            $response = '';
            $user = $this->getUser();
            if (!$user) {
                $response = $this->handleResponse([],Response::HTTP_UNAUTHORIZED,'',self::ERRORMSG);
            }
            else {
                $aircraft = $entityManager->getRepository(Aircraft::class)->find($id);
                if (!$aircraft) {
                    $response = $this->handleResponse([],Response::HTTP_NOT_FOUND,'',self::ERRORMSGTWO);
                } else {
                    $entityManager->remove($aircraft);
                    $entityManager->flush();
                    $response = $this->handleResponse([],Response::HTTP_OK,'Aircraft deleted successfully','');
                }
            }
        }catch(Exception $e){
            return $this->handleResponse([],Response::HTTP_INTERNAL_SERVER_ERROR,'',$e->getMessage());
        }
        return $response;
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
