<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/events', name: 'app_api_event_')]
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
            } else {
                return new JsonResponse(['status' => 'Bad request, order not found'], 400);
            }
        }

        if (!$order) {
            $order['eventDate'] = 'ASC';
        }

        $events = $em->getRepository(Event::class)->findBy([], $order);
        $data = $this->getData($events, []);

        return new JsonResponse($data);
    }

    #[Route('/created', name: 'listcreated', methods: ['GET'])]
    public function listCreated(EntityManagerInterface $em): JsonResponse
    {
        $profile = $this->getUser();
        $events = $em->getRepository(Event::class)->findBy(['creator' => $profile->getId()]);
        $data = $this->getData($events, []);

        return new JsonResponse($data);
    }

    #[Route('/upcoming', name: 'upcoming', methods: ['GET'])]
    public function upcoming(EntityManagerInterface $em): JsonResponse
    {
        $now = new \DateTime();
        $events = $em->getRepository(Event::class)->createQueryBuilder('e')
            ->where('e.eventDate > :now')
            ->setParameter('now', $now)
            ->orderBy('e.eventDate', 'ASC')
            ->getQuery()
            ->getResult();

        $data = $this->getData($events, []);

        return new JsonResponse($data);
    }

    #[Route('/filter', name: 'filter', methods: ['GET'])]
    public function filter(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $filters = array_filter([
            'title'            => $request->query->get('title'),
            'description'      => $request->query->get('description'),
            'date_from'        => $request->query->get('date_from'),
            'date_to'          => $request->query->get('date_to'),
            'category'         => $request->query->get('category'),
            'max_participants' => $request->query->get('max_participants'),
        ], fn($v) => $v !== null && $v !== '');

        $events = $em->getRepository(Event::class)->findByFilters($filters);
        $data   = $this->getData($events, []);

        return new JsonResponse($data);
    }

    #[Route('/categories', name: 'categories', methods: ['GET'])]
    public function categories(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();
        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'id'   => $category->getId(),
                'name' => $category->getName(),
                'img'  => $category->getImg(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/export/all', name: 'export_all', methods: ['GET'])]
    public function exportAll(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'No autenticado'], 401);
        }

        $filters = array_filter([
            'title'            => $request->query->get('title'),
            'description'      => $request->query->get('description'),
            'date_from'        => $request->query->get('date_from'),
            'date_to'          => $request->query->get('date_to'),
            'category'         => $request->query->get('category'),
            'max_participants' => $request->query->get('max_participants'),
        ], fn($v) => $v !== null && $v !== '');

        $events = empty($filters)
            ? $em->getRepository(Event::class)->findBy([], ['eventDate' => 'ASC'])
            : $em->getRepository(Event::class)->findByFilters($filters);

        $format = $request->query->get('format', 'csv');

        if ($format === 'csv') {
            $response = new StreamedResponse(function () use ($events) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, ['ID', 'Título', 'Descripción', 'Fecha', 'Ubicación', 'Aforo máximo', 'Inscritos', 'Público', 'Verificado'], ';');
                foreach ($events as $event) {
                    fputcsv($handle, [
                        $event->getId(),
                        $event->getTitle(),
                        $event->getDescription(),
                        $event->getEventDate()?->format('Y-m-d H:i:s'),
                        $event->getLocation(),
                        $event->getMaxParticipants(),
                        $event->getUsers()->count(),
                        $event->isPublic() ? 'Sí' : 'No',
                        $event->isVerified() ? 'Sí' : 'No',
                    ], ';');
                }
                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="eventos.csv"');

            return $response;
        }

        if ($format === 'json') {
            $data = $this->getData($events, []);
            return new JsonResponse($data);
        }

        return new JsonResponse(['message' => 'Formato no soportado. Usa csv o json'], 400);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository
    ): JsonResponse {
        /** @var User $creator */
        $creator = $this->getUser();

        if (!$creator) {
            return new JsonResponse(['message' => 'No autenticado'], 401);
        }

        $data = json_decode($request->getContent(), true);

        $category_id = $data['category_id'] ?? null;
        $category = $categoryRepository->find($category_id);

        if (!$category) {
            return new JsonResponse(['status' => 'Bad request, category not found'], 400);
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
            'data'   => [
                'id'               => $event->getId(),
                'title'            => $event->getTitle(),
                'description'      => $event->getDescription(),
                'event_date'       => $event->getEventDate()?->format('Y-m-d H:i:s'),
                'location'         => $event->getLocation(),
                'max_participants' => $event->getMaxParticipants(),
                'isPublic'         => $event->isPublic(),
                'isVerified'       => $event->isVerified(),
                'category'         => [
                    'id'   => $category->getId(),
                    'name' => $category->getName(),
                ],
                'creator'          => [
                    'id'   => $creator->getId(),
                    'name' => $creator->getName(),
                ],
            ],
        ], 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(EntityManagerInterface $em, int $id): JsonResponse
    {
        $event = $em->getRepository(Event::class)->find($id);
        if (!$event) {
            return new JsonResponse(['message' => 'Event not found'], 404);
        }
        $data = $this->getData([$event], []);

        return new JsonResponse($data[0]);
    }

    #[Route('/{user_id}/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        int $id,
        int $user_id,
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository
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

    #[Route('/{id}/attending', name: 'listIn', methods: ['GET'])]
    public function listInEvent(EntityManagerInterface $em, int $id): JsonResponse
    {
        $user = $em->getRepository(User::class)->find(['id' => $id]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }

        $events = $user->getAttendingEvents()->toArray();
        $data = $this->getData($events, []);

        return new JsonResponse($data);
    }

    #[Route('/{id}/join', name: 'join', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function join(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'No autenticado'], 401);
        }

        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Evento no encontrado'], 404);
        }

        if ($event->getUsers()->contains($user)) {
            return new JsonResponse(['message' => 'Ya estás inscrito en este evento'], 409);
        }

        $maxParticipants = $event->getMaxParticipants();
        if ($maxParticipants !== null && $event->getUsers()->count() >= $maxParticipants) {
            return new JsonResponse(['message' => 'El evento ha alcanzado su capacidad máxima'], 409);
        }

        $event->addUser($user);
        $em->flush();

        return new JsonResponse([
            'message'      => 'Inscripción realizada correctamente',
            'participants' => $event->getUsers()->count(),
        ]);
    }

    #[Route('/{id}/leave', name: 'leave', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function leave(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'No autenticado'], 401);
        }

        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Evento no encontrado'], 404);
        }

        if (!$event->getUsers()->contains($user)) {
            return new JsonResponse(['message' => 'No estás inscrito en este evento'], 409);
        }

        $event->removeUser($user);
        $em->flush();

        return new JsonResponse([
            'message'      => 'Inscripción cancelada correctamente',
            'participants' => $event->getUsers()->count(),
        ]);
    }

    #[Route('/{id}/export', name: 'export', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function export(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'No autenticado'], 401);
        }

        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Evento no encontrado'], 404);
        }

        $format = $request->query->get('format', 'csv');
        $participants = $event->getUsers()->toArray();

        if ($format === 'csv') {
            $response = new StreamedResponse(function () use ($event, $participants) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, ['Nombre', 'Apellido', 'Email'], ';');
                foreach ($participants as $participant) {
                    fputcsv($handle, [
                        $participant->getName(),
                        $participant->getSurname(),
                        $participant->getEmail(),
                    ], ';');
                }
                fclose($handle);
            });

            $filename = 'asistentes_' . preg_replace('/[^a-z0-9]/i', '_', $event->getTitle()) . '.csv';
            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

            return $response;
        }

        if ($format === 'json') {
            $data = [];
            foreach ($participants as $participant) {
                $data[] = [
                    'name'    => $participant->getName(),
                    'surname' => $participant->getSurname(),
                    'email'   => $participant->getEmail(),
                ];
            }

            return new JsonResponse($data);
        }

        return new JsonResponse(['message' => 'Formato no soportado. Usa csv o json'], 400);
    }

    public function getData(array $events, array $data): array
    {
        foreach ($events as $event) {
            $categories = [];
            foreach ($event->getCategories() as $category) {
                $categories[] = [
                    'id'   => $category->getId(),
                    'name' => $category->getName(),
                    'img'  => $category->getImg(),
                ];
            }

            $users = [];
            foreach ($event->getUsers() as $u) {
                $users[] = [
                    'id'   => $u->getId(),
                    'name' => $u->getName(),
                ];
            }

            $data[] = [
                'id'               => $event->getId(),
                'title'            => $event->getTitle(),
                'description'      => $event->getDescription(),
                'event_date'       => $event->getEventDate()?->format('Y-m-d H:i:s'),
                'location'         => $event->getLocation(),
                'max_participants' => $event->getMaxParticipants(),
                'isPublic'         => $event->isPublic(),
                'isVerified'       => $event->isVerified(),
                'categories'       => $categories,
                'participants'     => $users,
            ];
        }

        return $data;
    }
}
