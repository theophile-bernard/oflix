<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Model\MovieModel;
use App\Repository\CastingRepository;
use App\Repository\MovieRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    #[Route('/', name: 'front_main_home')]
    #[Route('/search', name: 'front_main_search')]
    public function home(MovieRepository $mv, Request $request) : Response
    {
        $search = $request->query->get("search");

        $movies = $mv->findAllOrderByTitleAscQB($search);

        return $this->render('front/main/home.html.twig', [
            "movies" => $movies,
        ]);
    }

    #[Route('/movies', name: 'front_main_index')]
    public function index(MovieRepository $mv) : Response
    {

        return $this->render('front/main/home.html.twig', [
            "movies" => $mv->findAllOrderByDateDescQB(),
        ]);
    }

    #[Route('/show/{slug<[-\w]+>}', name: 'front_main_show')]
    public function show(Movie $mv = null, CastingRepository $castingRepository) : Response
    {
        // dd($movie);
        if($mv === null)
        {
            // Pour renvoyer vers une 404 (non existante pour le moment -> renvoie une erreur symfony)
            // throw $this->createNotFoundException('Ce film n\'existe pas');

            $this->addFlash(
                'info',
                ["message" => 'Ce film n\'existe pas dans la base de données, voici les derniers films proposés',
                "title" => ""]
            );

            return $this->redirectToRoute('front_main_home');

        }
        
        return $this->render('front/main/show.html.twig', [
            "movie" => $mv,
            "casting" => $castingRepository->findCastingsForMovie($mv)
        ]);
    }

    #[Route('/show/{slug<[-\w]+>}/add-review', name: 'front_main_show_add_review', methods: ["GET", "POST"])]
    #[IsGranted('ROLE_USER')]
    public function addReview(Movie $mv = null, Request $request, EntityManagerInterface $em, ReviewRepository $reviewRepository) : Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if($mv === null)
        {
            // Pour renvoyer vers une 404 (non existante pour le moment -> renvoie une erreur symfony)
            // throw $this->createNotFoundException('Ce film n\'existe pas');

            $this->addFlash(
                'info',
                ["message" => 'Ce film n\'existe pas dans la base de données, voici les derniers films proposés',
                "title" => ""]
            );

            return $this->redirectToRoute('front_main_home');
        }

        $review = new Review;

        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $review->setMovie($mv);

            $em->persist($review);
            $em->flush();

            $this->addFlash(
                'success',
                ["message" => 'La critique a été ajouté au film.',
                "title" => ""]
            );

            // Substituer dans le EventListener ReviewInsertUpdate
            // on apelle une requête personnalisée qui calcule la moyenne
            // $averageRating = $reviewRepository->averageRating($mv);
            // on modifie le Movie
            // $mv->setRating($averageRating);
            // $em->flush();
            
            return $this->redirectToRoute('front_main_show', ["slug" => $mv->getSlug()]);
        }

        return $this->render('front/main/add-review.html.twig', [
            "form" => $form,
            "movie" => $mv
        ]);
    }

    #[Route('/switch', name: 'front_main_switcher')]
    public function switcher(SessionInterface $session) : Response
    {
        $theme = $session->get('theme');

        // et le permute dans l'autre thème netflix <-> allocine
        if ($theme === 'netflix') {
            $session->set('theme', 'allocine');
        } else {
            $session->set('theme', 'netflix');
        }

        $this->addFlash(
            'success', ["message" => "Votre thème a bien été modifié", "title" => ""]
            
        );

        return $this->redirectToRoute('front_main_home');
    }

    #[Route('/api/movies', name: 'front_api_movies')]
    public function api_movies() : Response
    {
        // On doit récupérer la liste des films
        $movies = MovieModel::getMovies();

        return $this->json(['movies' => $movies]);
    }

}