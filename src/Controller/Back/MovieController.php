<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Service\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/movie')]
class MovieController extends AbstractController
{
    #[Route('/', name: 'app_back_movie_index', methods: ['GET'])]
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('back/movie/index.html.twig', [
            'movies' => $movieRepository->findAllOrderByTitleAscQB(),
        ]);
    }

    #[Route('/new', name: 'app_back_movie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MySlugger $slugger): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movie->setSlug($slugger->slugTitle($movie->getTitle()));
            $entityManager->persist($movie);
            $entityManager->flush();

            $this->addFlash(
                'success',
                ["message" => ($movie->getType() === "Film" ? "Le film " : "La série ") . "\"" . $movie->getTitle() . "\" a bien été ajouté.",
                "title" => ""]
            );

            return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_movie_show', methods: ['GET'])]
    public function show(Movie $movie = null): Response
    {
        if($movie === null)
        {
            // Pour renvoyer vers une 404 (non existante pour le moment -> renvoie une erreur symfony)
            // throw $this->createNotFoundException('Ce film n\'existe pas');

            $this->addFlash(
                'info',
                ["message" => 'Ce casting n\'existe pas dans la base de données.',
                "title" => ""]
            );

            return $this->redirectToRoute('app_back_casting_index');

        }
        return $this->render('back/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_back_movie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Movie $movie = null, EntityManagerInterface $entityManager): Response
    {
        if($movie === null)
        {
            // Pour renvoyer vers une 404 (non existante pour le moment -> renvoie une erreur symfony)
            // throw $this->createNotFoundException('Ce film n\'existe pas');

            $this->addFlash(
                'info',
                ["message" => 'Ce casting n\'existe pas dans la base de données.',
                "title" => ""]
            );

            return $this->redirectToRoute('app_back_casting_index');

        }
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_movie_delete', methods: ['POST'])]
    public function delete(Request $request, Movie $movie = null, EntityManagerInterface $entityManager): Response
    {
        if($movie === null)
        {
            // Pour renvoyer vers une 404 (non existante pour le moment -> renvoie une erreur symfony)
            // throw $this->createNotFoundException('Ce film n\'existe pas');

            $this->addFlash(
                'info',
                ["message" => 'Ce casting n\'existe pas dans la base de données.',
                "title" => ""]
            );

            return $this->redirectToRoute('app_back_casting_index');

        }
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($movie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
    }
}
