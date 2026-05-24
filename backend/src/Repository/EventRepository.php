<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.categories', 'c')
            ->addSelect('c');

        if (!empty($filters['title'])) {
            $qb->andWhere('e.title LIKE :title')
                ->setParameter('title', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['description'])) {
            $qb->andWhere('e.description LIKE :description')
                ->setParameter('description', '%' . $filters['description'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $qb->andWhere('e.eventDate >= :date_from')
                ->setParameter('date_from', new \DateTime($filters['date_from']));
        }

        if (!empty($filters['date_to'])) {
            $qb->andWhere('e.eventDate <= :date_to')
                ->setParameter('date_to', new \DateTime($filters['date_to']));
        }

        if (!empty($filters['category'])) {
            $qb->andWhere('c.id = :category')
                ->setParameter('category', (int) $filters['category']);
        }

        if (!empty($filters['max_participants'])) {
            $qb->andWhere('e.maxParticipants <= :max_participants')
                ->setParameter('max_participants', (int) $filters['max_participants']);
        }

        return $qb->orderBy('e.eventDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
