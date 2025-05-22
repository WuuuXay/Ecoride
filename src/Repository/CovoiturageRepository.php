<?php

namespace App\Repository;

use App\Entity\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

public function findByDepartArriveeDate(?string $depart, ?string $arrivee, ?\DateTimeInterface $date, ?float $prixMax = null, ?bool $ecologique = null)
{
    $qb = $this->createQueryBuilder('c');

    if ($depart) {
        $qb->andWhere('c.depart LIKE :depart')
           ->setParameter('depart', '%' . $depart . '%');
    }

    if ($arrivee) {
        $qb->andWhere('c.arrivee LIKE :arrivee')
           ->setParameter('arrivee', '%' . $arrivee . '%');
    }

    if ($date) {
        $qb->andWhere('c.dateDepart >= :date')
           ->setParameter('date', $date);
    }

    if ($prixMax !== null) {
        $qb->andWhere('c.prix <= :prixMax')
           ->setParameter('prixMax', $prixMax);
    }

    if ($ecologique) {
        $qb->andWhere('c.ecologique = true');
    }

    return $qb->orderBy('c.dateDepart', 'ASC')
              ->getQuery()
              ->getResult();
}

public function findLastWeekDates(): array
{
    $qb = $this->createQueryBuilder('c')
        ->select('c.dateDepart')
        ->where('c.dateDepart >= :lastWeek')
        ->setParameter('lastWeek', new \DateTime('-7 days'))
        ->orderBy('c.dateDepart', 'ASC');

    $results = $qb->getQuery()->getResult();

    // Regroupement côté PHP par date (au format Y-m-d)
    $dates = [];
    foreach ($results as $result) {
        $dates[] = $result['dateDepart']->format('Y-m-d');
    }

    return array_unique($dates);
}

public function findCovoituragesProblematiques(): array
{
    return $this->createQueryBuilder('c')
        ->where('c.incident = true')
        ->leftJoin('c.chauffeur', 'chauffeur')
        ->leftJoin('c.participations', 'p')
        ->leftJoin('p.passager', 'passager')
        ->addSelect('chauffeur')
        ->addSelect('p')
        ->addSelect('passager')
        ->orderBy('c.dateDepart', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findLastWeekCounts(): array
{
    $qb = $this->createQueryBuilder('c')
        ->select('c.dateDepart')
        ->where('c.dateDepart >= :lastWeek')
        ->setParameter('lastWeek', new \DateTime('-7 days'))
        ->orderBy('c.dateDepart', 'ASC');

    $results = $qb->getQuery()->getResult();

    $counts = [];
    foreach ($results as $result) {
        $date = $result['dateDepart']->format('Y-m-d');
        if (!isset($counts[$date])) {
            $counts[$date] = 0;
        }
        $counts[$date]++;
    }

    return array_values($counts);
}

public function findLastWeekCredits(): array
{
    $qb = $this->createQueryBuilder('c')
        ->select('c.dateDepart, c.prix')
        ->where('c.dateDepart >= :lastWeek')
        ->setParameter('lastWeek', new \DateTime('-7 days'))
        ->orderBy('c.dateDepart', 'ASC');

    $results = $qb->getQuery()->getResult();

    $credits = [];
    foreach ($results as $result) {
        $date = $result['dateDepart']->format('Y-m-d');
        if (!isset($credits[$date])) {
            $credits[$date] = 0;
        }
        $credits[$date] += $result['prix'];
    }

    return array_values($credits);
}

}
