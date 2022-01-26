<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function getBooksSumValue()
    {
        return $this->createQueryBuilder('b')
            ->select("sum(b.price * b.nbrCopies)")
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function searchBooks($keyword, $priceMax, $categories,$authors,$editors)
    {
        $query = $this->createQueryBuilder('b');
        $query->andWhere("b.title LIKE :keyword");
        $query->setParameter('keyword', '%'.$keyword.'%');
        if($categories != null){
            $query->andWhere("b.category IN (:categories)");
            $query->setParameter('categories', $categories);
        }
        if($priceMax!=null){
            $query->andWhere("b.price <= :priceMax");
            $query->setParameter('priceMax', $priceMax);
        }
        if($authors!=null){
           $query->innerJoin("b.authors","a");
           $query->andWhere("a.id IN (:authors)");
           $query->setParameter(":authors",$authors);
        }

        if($editors!=null){
            $query->andWhere("b.editor IN (:editors)");
            $query->setParameter(":editors",$editors);
         }
       
        return $query->getQuery()->getResult();
    }

    /*

     if(!empty($categories)){
            $query->andWhere("b.category_id IN (:categories)");
            $query->setParameter('categories', $categories);
        }
        
    */


    // /**
    //  * @return Book[] Returns an array of Book objects
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
    public function findOneBySomeField($value): ?Book
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
