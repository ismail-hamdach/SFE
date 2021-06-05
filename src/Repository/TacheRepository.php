<?php

namespace App\Repository;

use App\Entity\Tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tache|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tache|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tache[]    findAll()
 * @method Tache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    /**
     * @return Tache[] Returns an array of Tache objects
     */
    
    public function findByProjet($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.projet = :val')
            ->setParameter('val', $value)
            ->orderBy('t.etat, t.dateCreation', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Tache[] Returns an array of Tache objects
     */
    
    public function findByEmploye($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.employe = :val')
            ->setParameter('val', $value)
            ->orderBy('t.dateCreation', 'ASC')
            ->orderBy('t.projet', 'ASC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    
    public function AvancementProjet($value): float
    {
        $nbrTermine = count($this->createQueryBuilder('t')
        ->where('t.projet = :val')
        ->setParameter('val', $value)
        ->andWhere('t.etat = true')
        ->getQuery()
        ->getResult());

        $nbrTotale = count($this->createQueryBuilder('t')
        ->where('t.projet = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getResult());
        return  round($nbrTermine / ($nbrTotale != 0 ? $nbrTotale : 1), 2); 
        ;
    }
    
}
