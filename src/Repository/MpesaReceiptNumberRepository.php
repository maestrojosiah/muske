<?php

namespace App\Repository;

use App\Entity\MpesaReceiptNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MpesaReceiptNumber|null find($id, $lockMode = null, $lockVersion = null)
 * @method MpesaReceiptNumber|null findOneBy(array $criteria, array $orderBy = null)
 * @method MpesaReceiptNumber[]    findAll()
 * @method MpesaReceiptNumber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MpesaReceiptNumberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MpesaReceiptNumber::class);
    }

    // /**
    //  * @return MpesaReceiptNumber[] Returns an array of MpesaReceiptNumber objects
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
    public function findOneBySomeField($value): ?MpesaReceiptNumber
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
