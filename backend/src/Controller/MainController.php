<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\GroupRepository;
use App\Repository\EventRepository;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        EventRepository $eventRepository
    ): Response
    {
        $usersCount = $userRepository->count([]);
        $groupsCount = $groupRepository->count([]);
        $eventsCount = $eventRepository->count([]);
        $lastUsers = $userRepository->findBy([], ['createdAt' => 'DESC'], 5);

        // Próximos 5 eventos (eventDate >= ahora)
        $upcomingEvents = $eventRepository->createQueryBuilder('e')
            ->where('e.eventDate >= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.eventDate', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('main/index.html.twig', [
            'usersCount' => $usersCount,
            'groupsCount' => $groupsCount,
            'eventsCount' => $eventsCount,
            'lastUsers' => $lastUsers,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }
}
