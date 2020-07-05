<?php

namespace App\Repository;

use App\Entity\Callback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Callback|null find($id, $lockMode = null, $lockVersion = null)
 * @method Callback|null findOneBy(array $criteria, array $orderBy = null)
 * @method Callback[]    findAll()
 * @method Callback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Callback::class);
    }

    // /**
    //  * @return Callback[] Returns an array of Callback objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Callback
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
