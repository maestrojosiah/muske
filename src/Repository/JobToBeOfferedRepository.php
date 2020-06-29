<?php

namespace App\Repository;

use App\Entity\JobToBeOffered;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method JobToBeOffered|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobToBeOffered|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobToBeOffered[]    findAll()
 * @method JobToBeOffered[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobToBeOfferedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobToBeOffered::class);
    }

    public function searchFromRoles($searchText)
    {
        return $this->createQueryBuilder('j')
           ->where('j.jobtitle')
           ->setParameter('input', '%' .$searchText.'%')
           ->setMaxResults(10)
           ->orderBy('j.jobtitle', 'ASC')
           ->getQuery()
           ->getResult();
    }

    /**
     * @return JobToBeOffered[] Returns an array of JobToBeOffered objects
     */
    
    public function findSpecialtiesList()
    {
        return $this->createQueryBuilder('s')
            ->select('s.jobtitle')
            ->andWhere('s.jobtitle IS NOT NULL')
            ->setMaxResults(1000)
            ->orderBy('s.jobtitle', 'ASC')
            ->distinct()
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return JobToBeOffered[] Returns an array of JobToBeOffered objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JobToBeOffered
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
