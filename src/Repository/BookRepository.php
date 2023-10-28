<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
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

    public function recherchebyref($id)
    {
        return $this->createQueryBuilder('b')
        ->where ('b.id=:id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
    }

    public function triauthorbook()
    {
        return $this->createQueryBuilder('b')
        ->join('b.author','a')
        ->addSelect('a')
        ->orderBy('a.username','ASC')
        ->getQuery()
        ->getResult();
    }

    public function listebook4()
    {
        return $this->createQueryBuilder('b')
        ->join('b.author','a')
        ->addSelect('a')
        ->where ("b.date<:date")
        ->andWhere('a.nb_books > :l')
        ->setParameter('date',new \DateTime('2023-01-01'))
        ->setParameter('l','35')
        ->getQuery()
        ->getResult();
    }
    public function changecategory()
    {
        return $this->createQueryBuilder('b')
        ->join('b.author','a')
        ->addSelect('a')
        ->where('a.username LIKE :username')
        ->setParameter('username','William Shakespear')
        ->getQuery()
        ->getResult();
    }
    public function affichecategory()
    {
        $em=$this->getEntityManager();
        $query=$em->createQuery('SELECT b FROM App\Entity\Book b where b.category LIKE :category ')
        ->setParameter('category','Science-Fiction');
        return $query->getResult();
        
    }
    public function affiche2dates()
    {
        $em=$this->getEntityManager();
        $query=$em->createQuery('SELECT b FROM App\Entity\Book b where b.date between :min and :max ')
        ->setParameter('min',new \DateTime('2014-01-01'))
        ->setParameter('max',new \DateTime('2018-12-31'));
        return $query->getResult();

        
    }

    
//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
