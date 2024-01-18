<?php

namespace App\Repository;

use App\Entity\Movie;
use App\Entity\Casting;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Casting>
 *
 * @method Casting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Casting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Casting[]    findAll()
 * @method Casting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CastingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casting::class);
    }


    /**
     * Permet par une jointure de récupérer un une seule requête tous les acteurs du film
     *
     * @param Movie $movie
     * @return array Movie
     */
    public function findCastingsForMovie(Movie $movie): array
    {
        // on crée la requête sur l'objet Casting ('c')
        return $this->createQueryBuilder('c')
            // il faut aussi retourner l'objet Person, c'est ce qu'on recherche
            ->addSelect('p')
            // on fait la jointure grace à la relation ManyToOne entre Casting et Person
            ->innerJoin('c.person', 'p')
            ->orderBy('c.castingOrder', 'ASC')
            ->andWhere('c.movie = :movie')
            ->setParameter('movie', $movie)
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByMovieAscQB(string $search = null): array
    {
         $qb = $this->createQueryBuilder('c')->innerJoin("c.movie", 'm')->orderBy('m.title', 'ASC')->addOrderBy('c.castingOrder', "ASC");
         
         if ($search)
         {
             $qb->andWhere('m.title LIKE :param')->setParameter("param", "%" . $search . "%");
         }
 
        return $qb->getQuery()->getResult();
    }
 

//    /**
//     * @return Casting[] Returns an array of Casting objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Casting
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
