<?php

namespace App\Repository;

use App\Constants\Constants;
use App\Entity\Borrowing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Borrowing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Borrowing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Borrowing[]    findAll()
 * @method Borrowing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BorrowingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Borrowing::class);
    }
    public function numberOfRequestedBooks()
    {
        return $this->createQueryBuilder('b')
            ->select("count(b)")
            ->where("b.status = :req ")
            ->setParameter("req",Constants::REQUESTED)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    public function numberOfUnreturnedBooks($constants)
    {
        return $this->createQueryBuilder('b')
            ->select("count(b)")
            ->where("b.status in (:nvr,:bor)")
            ->setParameter('nvr', $constants[0])
            ->setParameter('bor', $constants[0])
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function userWeeklyRequests($uid){
        return $this->createQueryBuilder('b')
                ->andWhere('WEEK(b.requestDateTime)=WEEK(NOW())')
                ->andWhere('b.user = :uid')
                ->andWhere('b.status = :req')
                ->setParameter("req",Constants::REQUESTED)
                ->setParameter('uid',$uid)
                ->orderBy('b.requestDateTime','ASC')
                ->getQuery()
                ->getResult();
    }

    public function userWeeklyUnreturned($uid){
        return $this->createQueryBuilder('b')
                ->andWhere('WEEK(b.requestDateTime)=WEEK(NOW())')
                ->andWhere('b.user = :uid')
                ->andWhere('b.status in (:nvr,:bor)')
                ->setParameter('nvr', Constants::NEVER_RETURNED)
                ->setParameter('bor', Constants::BORROWED)
                ->setParameter('uid',$uid)
                ->orderBy('b.requestDateTime','ASC')
                ->getQuery()
                ->getResult();
    }

    // /**
    //  * @return Borrowing[] Returns an array of Borrowing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Borrowing
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
