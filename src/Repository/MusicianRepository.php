<?php

namespace App\Repository;

use App\Entity\Musician;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Musician|null find($id, $lockMode = null, $lockVersion = null)
 * @method Musician|null findOneBy(array $criteria, array $orderBy = null)
 * @method Musician[]    findAll()
 * @method Musician[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusicianRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Musician::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Musician) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function flush()
    {
        $this->_em->flush();
    }    

    /**
     * @return Musician[] Returns an array of Musician objects
     */
    
    public function getMusicians($account, $limit = 100)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.account = :val')
            ->setParameter('val', $account)
            ->orderBy('RAND()')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * @return Musician[] Returns an array of Musician objects
     */
    
    public function findTitlesList()
    {
        return $this->createQueryBuilder('s')
            ->select('s.title')
            ->leftJoin('s.settings', 'st')
            ->andWhere('s.title IS NOT NULL')
            ->andWhere('st.visibility = :on')
            ->setParameter('on', 'on')
            ->setMaxResults(100)
            ->orderBy('s.title', 'ASC')
            ->distinct()
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Musician[] Returns an array of Musician objects
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
    public function findOneBySomeField($value): ?Musician
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
