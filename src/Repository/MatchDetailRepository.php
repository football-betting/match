<?php

namespace App\Repository;

use App\Entity\MatchDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MatchDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatchDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatchDetail[]    findAll()
 * @method MatchDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchDetail::class);
    }

    // /**
    //  * @return MatchDetail[] Returns an array of MatchDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MatchDetail
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
