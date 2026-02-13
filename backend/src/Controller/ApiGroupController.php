<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/group', name: 'app_api_group')]
class ApiGroupController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $allowedFields = ['name', 'description'];
        $order = [];

        $orderParams = $request->query->all('order');

        foreach ($orderParams as $field => $direction) {
            if (in_array($field, $allowedFields, true) &&
                in_array(strtoupper($direction), ['ASC', 'DESC'], true)
            ) {
                $order[$field] = strtoupper($direction);
            }
        }

        if (!$order) {
            $order['name'] = 'ASC';
        }

        $groups = $em->getRepository(Group::class)->findBy([], $order);
        $data = [];

        $data = $this->getData($groups, $data);

        return new JsonResponse($data);
    }

    #[Route('/{id}/created', name: 'listcreated', methods: ['GET'])]
    public function listCreated(EntityManagerInterface $em, int $id): JsonResponse
    {
        $groups = $em->getRepository(Group::class)->findBy(['creator_id' => $id]);
        $data = [];

        $data = $this->getData($groups, $data);
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
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $creator_id = $data['creator_name'] ?? null;
        $creator = $userRepository->find($creator_id);

        if (!$creator) {
            return new JsonResponse(['status' => 'Bad request, creator not found'], 400);
        }

        $group = new Group();
        $group->setName($data['name'] ?? null);
        $group->setDescription($data['description'] ?? null);
        $group->setIsPrivate($data['is_private'] ?? false);
        $group->setCreator($creator);

        $em->persist($group);
        $em->flush();

        return new JsonResponse([
            'status' => 'Group created',
            'data' => [
                'id' => $group->getId(),
                'name' => $group->getName(),
                'description' => $group->getDescription(),
                'is_private' => $group->IsPrivate(),
                'creator' => [
                    'id' => $creator->getId(),
                    'name' => $creator->getName(),
                ],
            ],
        ], 201);
    }


    /**
     * @param array $groups
     * @param array $data
     * @return array
     */
    public function getData(array $groups, array $data): array
    {

        $responsibles = [];

        foreach ($groups as $group) {

            foreach ($group->getResponsibles() as $responsible) {
                $responsibles[] = [
                    'id' => $responsible->getId(),
                    'name' => $responsible->getName(),
                ];
            }

            $data[] = [
                'id' => $group->getId(),
                'name' => $group->getName(),
                'description' => $group->getDescription(),
                'created_at' => $group->getCreatedAt()?->format('Y-m-d H:i:s'),
                'is_private' => $group->isPrivate(),
                'responsibles' => $responsibles,
            ];
        }
        return $data;
    }
}
