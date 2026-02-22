<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/groups', name: 'app_api_group')]
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

    #[Route('/created', name: 'listcreated', methods: ['GET'])]
    public function listCreated(EntityManagerInterface $em, int $id): JsonResponse
    {
        $user = $this->getUser();
        $groups = $em->getRepository(Group::class)->findBy(['creator' => $user->getId()]);
        $data = [];

        $data = $this->getData($groups, $data);
        return new JsonResponse($data);
    }
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $group = $em->getRepository(Group::class)->find($id);

        if (!$group) {
            return new JsonResponse(['message' => 'Group not found'], 404);
        }

        return new JsonResponse([
            'id'          => $group->getId(),
            'name'        => $group->getName(),
            'description' => $group->getDescription(),
            'is_private'  => $group->isPrivate(),
        ]);
    }

    #[Route('/{user_id}/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        int                    $id,
        int                    $user_id,
        Request                $request,
        EntityManagerInterface $em,
    ): JsonResponse {

        $group = $em->getRepository(Group::class)->find($id);

        if (!$group) {
            return new JsonResponse(['message' => 'Event not found'], 404);
        }
        if ($user_id !== $group->getCreator()->getId()) {
            return new JsonResponse(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $group->setName($data['name'] ?? $group->getName());
        $group->setDescription($data['description'] ?? $group->getDescription());
        $group->setIsPrivate($data['is_private'] ?? $group->isPrivate());

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
        foreach ($groups as $group) {
            $responsibles = [];
            $users = [];
            foreach ($group->getResponsibles() as $responsible) {
                $responsibles[] = [
                    'id' => $responsible->getId(),
                    'name' => $responsible->getName(),
                ];
            }

            foreach ($group->getUsers() as $user) {
                $users[] = [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                ];
            }

            $data[] = [
                'id' => $group->getId(),
                'name' => $group->getName(),
                'description' => $group->getDescription(),
                'created_at' => $group->getCreatedAt()?->format('Y-m-d H:i:s'),
                'is_private' => $group->isPrivate(),
                'responsibles' => $responsibles,
                'users' => $users,
            ];
        }
        return $data;
    }

}
