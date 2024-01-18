<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function findAllOrderByTitleAscDql()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT m
                FROM App\Entity\Movie m
                ORDER BY m.title ASC'
        );

        // retourne un tableau d'objet
        return $query->getResult();
    }

        /**
    * @return Movie[] Returns an array of Movie objects
    */
   public function findAllOrderByTitleAscQB(string $search = null): array
   {
        $qb = $this->createQueryBuilder('m')->orderBy('m.title', 'ASC');
        
        if ($search)
        {
            $qb->andWhere('m.title LIKE :param')->setParameter("param", "%" . $search . "%");
        }

       return $qb->getQuery()->getResult();
   }

       /**
    * @return Movie[] Returns an array of Movie objects
    */
    public function findAllOrderByDateDescQB(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.releaseDate ', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Movie[] Returns an array of Movie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Movie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
