<?php

namespace App\Controller\Api;

use ErrorException;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Service\MySlugger;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * Renvoi de la liste de tous les films avec quelques informations de base
     *
     * @param MovieRepository $movieRepository
     * @return JsonResponse
     */
    #[Route('/api/movies', name: 'app_movies_get', methods: ["GET"])]
    public function getCollection(MovieRepository $movieRepository): JsonResponse
    {
        // cette méthode met à disposition tous les movies de la base
        $movies = $movieRepository->findAll();
        return $this->json($movies,200,[],['groups' => 'get_collection']);
    }

    /**
     * Renvoi des détails d'un film donné pour affichage de ce film
     *
     * @param MovieRepository $movieRepository
     * @return JsonResponse
     */
    #[Route('/api/movies/{id<\d+>}', name: 'app_movies_get_item', methods: ["GET"])]
    public function getItem(Movie $movie = null): JsonResponse
    {
        // utilisation de la convention Yoda : inversion de la variable et de la valeur de comparaison
        if(null === $movie)
        {
            return $this->json(['message' => 'Le film demandé n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($movie,200,[],['groups' => 'get_item']);
    }

    /**
     * Récupération de tout les genres
     *
     * @param GenreRepository $genreRepository
     * @return JsonResponse
     */
    #[Route('/api/genres', name: 'app_genre_get', methods: ["GET"])]
    public function getGenres(GenreRepository $genreRepository): JsonResponse
    {
        $genres = $genreRepository->findAll();
        return $this->json($genres,200,[],['groups' => 'get_genres']);
    }

    /**
     * Récupération des films par genre
     * 
     * @param Genre $genre
     * @return JsonResponse
     */
    #[Route('/api/genres/{id<\d+>}/movies', name: 'app_genre_get_movies', methods: ["GET"])]
    public function getMoviesByGenre(Genre $genre = null): JsonResponse
    {
        // dd($genre);
        // if($genre === null)
        // {
        //     return $this->json("Le genre demandé n'existe pas", 404);
        // }

        if($genre === null)
        {
            return $this->json(['message' => 'Le genre demandé n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($genre,200,[],['groups' => 'get_genres_movies']);
    }

    #[Route('/api/movies/random', name: 'app_movie_random', methods: ["GET"])]
    public function getRandomMovie(MovieRepository $movieRepository): JsonResponse
    {

        $movie = $movieRepository->findOneByRandom();   
        if(!$movie)
        {
            throw $this->json(['message' => 'Le film demandé n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        // cette méthode met à disposition tous les movies de la base
        return $this->json($movie,200,[],['groups' => 'get_movie_ramdom']);
    }

    #[Route('/api/movies/ressource', name: 'app_movie_ressource', methods: ["POST"])]
    public function getMovieRessource(Request $request, 
                                      SerializerInterface $serializer, 
                                      EntityManagerInterface $entityManagerInterface, 
                                      MySlugger $mySlugger, 
                                      ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();
        $movie = $serializer->deserialize($jsonContent, Movie::class, 'json');

        $movie->setSlug($mySlugger->slugTitle($movie->getTitle()));

        $errors = $validator->validate($movie);

        $entityManagerInterface->persist($movie);
        $entityManagerInterface->flush();

        return $this->json($movie,201,[],['groups' => 'get_item']);
    }
    
    /**
     * Modification d'un film donné
     *
     * @param 
     * @return JsonResponse
     */
    #[Route('/api/movies/{id<\d+>}', name: 'edit', methods: ['PUT'])]
    public function edit(
        EntityManagerInterface $entityManager, 
        Movie $movie = null,
        MySlugger $mySlugger,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
        ): JsonResponse
    {
        // cette méthode modifie un film donné dans la base
        // on intercepte le traitement de la 404
        // Utilisation de la Yoda convention
        if (null === $movie) {
            return $this->json(['message' => "Le film demandé n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        // Le film existe, on le modifie
             // Récupérer les informations JSON
             $json = $request->getContent();
             // Attention lors des tests avec Postman, il faut rajoute un '/' à la fin de l'URL en POST
             // désérialisation du JSON pour obtenir un objet Movie
             // REFER : https://symfony.com/doc/6.4/serializer.html#serializer-context
             // Pour récupérer les genres, il faut utiliser un normalizer
             // qui transforme les identifiants du genre en objet Genre
             // REFER : https://gist.github.com/benlac/c9efc733ee16ebd0d438119bcccb92b9
             // REFER : https://symfony.com/doc/current/components/serializer.html#deserializing-an-object
             $serializer->deserialize($json, Movie::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $movie]);
     
             // on rajoute le slug à notre $movie
             $movie->setSlug($mySlugger->slugTitle($movie->getTitle()));
     
             // on valide l'entité reconstruite
             // REFER : https://symfony.com/doc/current/validation.html#using-the-validator-service
             $errors = $validator->validate($movie);

             dd($errors);
     
             // on vérifie si on a des erreurs
             // on utilise le Return Early Pattern
             if (count($errors)) {
                 return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
             }
     
             // flush

        $entityManager->flush();

        return $this->json($movie, Response::HTTP_OK, [], ['groups' => 'get_item']);
    }

    /**
     * Suppression d'un film donné
     *
     * @param Movie $movie
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/movies/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Movie $movie = null): JsonResponse
    {
        // cette méthode supprime un film donné de la base
        // on intercepte le traitement de la 404
        // Utilisation de la Yoda convention
        if (null === $movie) {
            return $this->json(['message' => "Le film demandé n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        // Le film existe, on le supprimer
        $entityManager->remove($movie);
        $entityManager->flush();

        return $this->json($movie, Response::HTTP_NO_CONTENT);
    }
}
