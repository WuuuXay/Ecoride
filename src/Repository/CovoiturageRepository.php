<?php

namespace App\Repository;

use App\Entity\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Covoiturage>
 */
class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

    public function findByDepartArriveeDate(?string $depart, ?string $arrivee, ?\DateTimeInterface $date)
    {
        $qb = $this->createQueryBuilder('c');
        
        if ($depart) {
            $qb->andWhere('c.depart LIKE :depart')
               ->setParameter('depart', '%'.$depart.'%');
        }
        
        if ($arrivee) {
            $qb->andWhere('c.arrivee LIKE :arrivee')
               ->setParameter('arrivee', '%'.$arrivee.'%');
        }
        
        if ($date) {
            $qb->andWhere('c.dateDepart >= :date')
               ->setParameter('date', $date);
        }
        
        return $qb->orderBy('c.dateDepart', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}