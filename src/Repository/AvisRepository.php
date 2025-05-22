<?php

namespace App\Repository;

use App\Entity\Avis;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 *
 * @method Avis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avis[]    findAll()
 * @method Avis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    /**
     * Obtenir tous les avis reçus par un utilisateur
     */
    public function findAvisRecus(Utilisateur $utilisateur): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.cible = :user')
            ->setParameter('user', $utilisateur)
            ->orderBy('a.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtenir tous les avis donnés par un utilisateur
     */
    public function findAvisDonnes(Utilisateur $utilisateur): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.auteur = :user')
            ->setParameter('user', $utilisateur)
            ->orderBy('a.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si un utilisateur a déjà laissé un avis à une autre personne
     */
    public function findAvisUnique(Utilisateur $auteur, Utilisateur $cible): ?Avis
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.auteur = :auteur')
            ->andWhere('a.cible = :cible')
            ->setParameter('auteur', $auteur)
            ->setParameter('cible', $cible)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
