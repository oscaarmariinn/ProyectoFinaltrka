<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\CategoryRepository;
use App\Repository\EventTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/events', name: 'app_api_event')]
class ApiEventController extends AbstractController
{

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $allowedFields = ['title', 'eventDate', 'maxParticipants', 'location'];
        $order = [];

        $orderParams = $request->query->all('order');

        foreach ($orderParams as $field => $direction) {
            if (in_array($field, $allowedFields, true) &&
                in_array(strtoupper($direction), ['ASC', 'DESC'], true)
            ) {
                $order[$field] = strtoupper($direction);
            }else{
                return new JsonResponse(['status' => 'Bad request, order not found'], 400);
            }
        }

        if (!$order) {
            $order['eventDate'] = 'ASC';
        }

        $events = $em->getRepository(Event::class)->findBy([], $order);
        $data = [];

        $data = $this->getData($events, $data);

        return new JsonResponse($data);
    }

    #[Route('/created/{id}', name: 'listcreated', methods: ['GET'])]
    public function listCreated(EntityManagerInterface $em, int $id): JsonResponse
    {
        $events = $em->getRepository(Event::class)->findBy(['creator' => $id]);
        $data = [];

        $data = $this->getData($events, $data);
        return new JsonResponse($data);
    }

    #[Route('/{user_id}/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        int                    $id,
        int                    $user_id,
        Request                $request,
        EntityManagerInterface $em,
        CategoryRepository     $categoryRepository
    ): JsonResponse {

        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Event not found'], 404);
        }
        if ($user_id !== $event->getCreator()->getId()) {
            return new JsonResponse(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $event->setTitle($data['title'] ?? $event->getTitle());
        $event->setDescription($data['description'] ?? $event->getDescription());
        $event->setLocation($data['location'] ?? $event->getLocation());
        $event->setMaxParticipants($data['max_participants'] ?? $event->getMaxParticipants());
        $event->setIsPublic($data['isPublic'] ?? $event->isPublic());

        if (isset($data['event_date'])) {
            $event->setEventDate(new \DateTime($data['event_date']));
        }

        if (isset($data['isPublic'])) {
            if ($event->isPublic() === 1) {
                $eventType = $em->getRepository(Event::class)->find(1);
                $event->setEventType($eventType);
            }
            if ($event->isPublic() === 0) {
                $eventType = $em->getRepository(Event::class)->find(2);
                $event->setEventType($eventType);
            }
        }

        if (isset($data['categories'])) {
            foreach ($event->getCategories() as $category) {
                $event->removeCategory($category);
            }
            foreach ($data['categories'] as $categoryId) {
                $category = $categoryRepository->find($categoryId);
                if ($category) {
                    $event->addCategory($category);
                } else {
                    return new JsonResponse(['status' => 'Bad request, category not found'], 400);
                }
            }
        }

        $em->flush();
        return new JsonResponse(['message' => 'Event updated successfully']);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository, EventTypeRepository $eventTypeRepository, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $category_id = $data['category_id'] ?? null;
        $category = $categoryRepository->find($category_id);

        $creator_id = $data['creator_name'] ?? null;
        $creator = $userRepository->find($creator_id);

        if (!$category) {
            return new JsonResponse(['status' => 'Bad request, category not found'], 400);
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
                'creator' => [
                    'id' => $creator->getId(),
                    'name' => $creator->getName(),
                ],
            ],
        ], 201);
    }

    /**
     * @param array $events
     * @param array $data
     * @return array
     */
    public function getData(array $events, array $data): array
    {
        foreach ($events as $event) {

            $categories = [];
            foreach ($event->getCategories() as $category) {
                $categories[] = [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                ];
            }

            $data[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'event_date' => $event->getEventDate()?->format('Y-m-d H:i:s'),
                'location' => $event->getLocation(),
                'max_participants' => $event->getMaxParticipants(),
                'isPublic' => $event->isPublic(),
                'isVerified' => $event->isVerified(),
                'categories' => $categories,
            ];
        }
        return $data;
    }
}

/*

 */
