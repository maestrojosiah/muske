<?php

namespace App\Repository;

use App\Entity\WebTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WebTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebTheme[]    findAll()
 * @method WebTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebTheme::class);
    }

    // /**
    //  * @return WebTheme[] Returns an array of WebTheme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WebTheme
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
