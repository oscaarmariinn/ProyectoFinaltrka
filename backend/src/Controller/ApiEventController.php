<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\EventType;
use App\Repository\CategoryRepository;
use App\Repository\EventTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/events', name: 'app_api_event')]
class ApiEventController extends AbstractController
{

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $events = $em->getRepository(Event::class)->findAll();
        $data = [];

        foreach ($events as $event) {
            $categories = $em->getRepository(Category::class)->findBy(['categoty_id' => $event->getId()], ['name' => 'ASC']);
            $eventTypes = $em->getRepository(EventType::class)->findBy(['event_type_id' => $event->getId()], ['name' => 'ASC']);
            $catego = [];
            $types = [];
            foreach ($categories as $category) {
                $catego[] = [
                    'id' => $category->getid(),
                    'name' => $category->getName(),
                ];
            }
            foreach ($eventTypes as $eventType) {
                $types[] = [
                    'id' => $eventType->getid(),
                    'name' => $eventType->getName(),
                ];
            }
            $data[] = [
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'event_date' => $event->getEventDate(),
                'location' => $event->getLocation(),
                'max_participants' => $event->getMaxParticipants(),
                'isPublic' => $event->isPublic(),
                'isVerified' => $event->isVerified(),
                'eventType' => $types,
                'categories' => $catego,

            ];
        }


        return new JsonResponse($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository, EventTypeRepository $eventTypeRepository, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $category_id = $data['category_id'] ?? null;
        $category = $categoryRepository->find($category_id);

        $eventType_id = $data['eventType_id'] ?? null;
        $eventType = $eventTypeRepository->find($eventType_id);

        $creator_id = $data['creator_name'] ?? null;
        $creator = $userRepository->find($creator_id);

        if (!$category) {
            return new JsonResponse(['status' => 'Bad request, category not found'], 400);
        }
        if (!$eventType) {
            return new JsonResponse(['status' => 'Bad request, eventType not found'], 400);
        }
        if (!$creator) {
            return new JsonResponse(['status' => 'Bad request, creator not found'], 400);
        }

        $event = new Event();
        $event->setTitle($data['title'] ?? null);
        $event->setDescription($data['description'] ?? null);
        $eventDate = isset($data['event_date']) ? new \DateTime($data['event_date']) : null;
        $event->setEventDate($eventDate);
        $event->setLocation($data['location'] ?? null);
        $event->setMaxParticipants($data['max_participants'] ?? null);
        $event->setIsPublic($data['isPublic'] ?? false);
        $event->addCategory($category);
        $event->setEventType($eventType);
        $event->setCreator($creator);

        $em->persist($event);
        $em->flush();

        return new JsonResponse([
            'status' => 'Event created',
            'data' => [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'event_date' => $event->getEventDate()?->format('Y-m-d H:i:s'),
                'location' => $event->getLocation(),
                'max_participants' => $event->getMaxParticipants(),
                'isPublic' => $event->isPublic(),
                'isVerified' => $event->isVerified(),
                'category' => [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                ],
                'eventType' => [
                    'id' => $eventType->getId(),
                    'name' => $eventType->getName(),
                ],
                'creator' => [
                    'id' => $creator->getId(),
                    'name' => $creator->getName(),
                ],
            ],
        ], 201);
    }
}

/*

 */
