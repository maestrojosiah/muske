<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }
    
    /**
     * @return Skill[] Returns an array of Skill objects
     */
    
    public function findSkillsList()
    {
        return $this->createQueryBuilder('s')
            ->select('s.skillname')
            ->setMaxResults(1000)
            ->orderBy('s.skillname', 'ASC')
            ->distinct()
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchFromSkills($searchText)
    {
        return $this->createQueryBuilder('s')
           ->where('s.skillname LIKE :input')               
           ->setParameter('input', '%' .$searchText.'%')
           ->setMaxResults(3)
           ->orderBy('s.skillname', 'ASC')
           ->getQuery()
           ->getResult();
    }


    // /**
    //  * @return Skill[] Returns an array of Skill objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Skill
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
