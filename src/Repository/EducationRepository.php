<?php

namespace App\Repository;

use App\Entity\Education;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Education|null find($id, $lockMode = null, $lockVersion = null)
 * @method Education|null findOneBy(array $criteria, array $orderBy = null)
 * @method Education[]    findAll()
 * @method Education[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EducationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Education::class);
    }

    /**
     * @return Education[] Returns an array of Education objects
     */
 
    public function findByGivenField($field, $sort, $musician)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.musician = :val')
            ->setParameter('val', $musician)
            ->orderBy("e.$field", $sort)
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;

    }
  

    /*
    public function findOneBySomeField($value): ?Education
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
