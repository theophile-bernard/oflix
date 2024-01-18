<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Model\MovieModel;
use App\Service\FavoriteManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FavoritesController extends AbstractController
{

    #[Route('/favorites', name: 'front_favorites_index')]
    public function index(SessionInterface $session): Response
    {

        return $this->render('front/favorites/favorites.html.twig');
    }

    #[Route('/favorites/add/{slug}', name: 'front_favorites_add', methods: ["POST"])]
    public function add(Movie $movie, /* SessionInterface $session, */ FavoriteManager $favoriteManager): Response
    {

        if ($movie === null) {
            throw $this->createNotFoundException("Le film demandé n'existe pas");
        }

        // $newMovie = $session->get("favorites");

        // $newMovie[$movie->getId()] = $movie;

        // $session->set('favorites', $newMovie);
        
        // on délègue toute la partie métier au service Favorites Manager
        if($favoriteManager->add($movie))
        {
            // on prépare un message flash
            // REFER : https://symfony.com/doc/current/session.html#flash-messages


            $this->addFlash(
                'success',
                [
                    "title" => $movie->getTitle(),
                    "message" => ' a été ajouté à votre liste de favoris'
            ]);
        } else {
            $this->addFlash(
                'warning',
                [
                    "title" => $movie->getTitle(),
                    "message" => ' fait déjà partie de votre liste de favoris.'
            ]);
        }
        return $this->redirectToRoute('front_favorites_index');
    }

    #[Route('/favorites/remove/{slug}', name: 'front_favorites_remove')]
    public function remove(Movie $movie, /* SessionInterface $session,  */ FavoriteManager $favoriteManager): Response
    {
        
        // $movies = $session->get("favorites");

        // unset($movies[$movie->getId()]);

        // $session->set('favorites', $movies);

        if($favoriteManager->remove($movie))
        {
            // on prépare un message flash
            // REFER : https://symfony.com/doc/current/session.html#flash-messages


            $this->addFlash(
                'success',
                [
                    "title" => $movie->getTitle(),
                    "message" => ' a été supprimé de votre liste de favoris.'
            ]);
        }

        return $this->redirectToRoute('front_favorites_index');
    }

    #[Route('/favorites/empty', name: 'front_favorites_empty')]
    public function empty(FavoriteManager $favoriteManager): Response
    {
        if($favoriteManager->empty())
        {
            $this->addFlash(
                'danger', [
                    "message" => 'Votre liste de favoris a été vidée'
            ]);
        }
        else{
            $this->addFlash(
                'danger', [
                    "message" => 'La liste des favoris ne peut pas être vidée.'
            ]);
        }

        return $this->redirectToRoute('front_favorites_index');
    }
}
