<?php

namespace App\Controller\Front;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Season;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManager;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MovieTestController extends AbstractController
{
    #[Route('/movie/test', name: 'app_movie_test_browse')]
    public function browse(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();
        dd($movies);
        return $this->render('front/movie_test/index.html.twig', [
            'controller_name' => 'MovieTestController',
        ]);
    }

    #[Route('/movie/test/add', name: 'app_movie_test_add')]
    public function add(EntityManagerInterface $entityManager): Response
    {
         // on récupère les données de data.php soit le tableau $shows
         include(__DIR__ . '/../../sources/data.php');
         // dd($shows);
         // on insère dana le BDD les données du tableau
         foreach ($shows as $show) {
             // l'objet de add est d'ajouter un film à la base
             $movie = new Movie;
             $movie->setTitle($show['title']);
             $movie->setReleaseDate(new \DateTimeImmutable($show['release_date']));
             $movie->setDuration($show['duration']);
             $movie->setSummary($show['summary']);
             $movie->setSynopsis($show['synopsis']);
             $movie->setPoster($show['poster']);
             $movie->setRating($show['rating']);
             $movie->setType($show['type']);
 
             // on veut ausii rajouter des saisons aux séries
             if ($movie->getType() === 'Série') {
                 // on crée une saison
                 $season = new Season;
                 $season->setNumber(1);
                 $season->setEpisodesNumber(rand(6,12));
                 // on doit associer la saison au film
                 $season->setMovie($movie);
                 // les deux sont équivalent, mais Symfony conseille de faire l'association du coté du owning side 
                 // $movie->addSeason($season);
                 $entityManager->persist($season);
             }
 
             // tell Doctrine you want to (eventually) save the Product (no queries yet)
             $entityManager->persist($movie);
         }
 
         // actually executes the queries (i.e. the INSERT query)
         $entityManager->flush();
         die();
 
         return $this->render('front/movie_test/index.html.twig', [
             'controller_name' => 'MovieTestController',
         ]);
     }

    #[Route('/movie/test/{id<\d+>}', name: 'app_movie_test_show')]
    public function show(Movie $movie = null): Response
    {
        if ($movie === null)
        {
            throw $this->createNotFoundException("Désolé ce film n'est pas encore dans notre catalogue");
        }
        dd($movie);
        return $this->render('front/movie_test/index.html.twig', [
            'controller_name' => 'MovieTestController',
        ]);
    }

    #[Route('/movie/test/{id<\d+>}/edit', name: 'app_movie_test_edit')]
    public function edit(Movie $movie, EntityManagerInterface $entityManager): Response
    {
        $movie->setDuration(245);
        $genre = new Genre;
        $genre->setName('Science Fiction');
        $entityManager->persist($genre);
        $movie->addGenre($genre);

        $entityManager->flush();
        dd($movie);

        return $this->render('front/movie_test/index.html.twig', [
            'controller_name' => 'MovieTestController',
        ]);
    }

    #[Route('/movie/test/{id<\d+>}/delete', name: 'app_movie_test_delete')]
    public function delete(Movie $movie, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($movie);
        $entityManager->flush();
        dd($movie);

        return $this->render('front/movie_test/index.html.twig', [
            'controller_name' => 'MovieTestController',
        ]);
    }

    #[Route('/genre/test', name: 'app_genre_test_browse')]
    public function gBrowse(GenreRepository $genreRepository): Response
    {
        $genre = $genreRepository->findAll();
        dd($genre);
        return $this->render('front/movie_test/index.html.twig', [
            'controller_name' => 'MovieTestController',
        ]);
    }
}
