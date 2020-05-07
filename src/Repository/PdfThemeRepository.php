<?php

namespace App\Repository;

use App\Entity\PdfTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PdfTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method PdfTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method PdfTheme[]    findAll()
 * @method PdfTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PdfThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PdfTheme::class);
    }

    // /**
    //  * @return PdfTheme[] Returns an array of PdfTheme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PdfTheme
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
