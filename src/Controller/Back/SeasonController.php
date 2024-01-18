<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/back/season')]
class SeasonController extends AbstractController
{
    #[Route('/{id<\d+>}', name: 'app_back_season_index', methods: ['GET'])]
    public function index(Movie $movie, SeasonRepository $seasonRepository): Response
    {
        return $this->render('back/season/index.html.twig', [
            'seasons' => $seasonRepository->findByMovie($movie, ["number" => "ASC"]),
            'movie' => $movie,
        ]);
    }

    #[Route('/new/{id<\d+>}', name: 'app_back_season_new', methods: ['GET', 'POST'])]
    public function new(Movie $movie, Request $request, EntityManagerInterface $entityManager): Response
    {
        $season = new Season();
        $season->setMovie($movie);
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($season);
            $entityManager->flush();

            $this->addFlash(
                'success',
                ["message" => 'La saison à bien été ajouté à la série ' . $request->request->get("movie"),
                "title" => ""]
            );

            return $this->redirectToRoute('app_back_season_index', ["id" => $movie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/season/new.html.twig', [
            'season' => $season,
            'form' => $form,
            'movie' => $movie
        ]);
    }

    #[Route('/{id<\d+>}/show/', name: 'app_back_season_show', methods: ['GET'])]
    public function show(Season $season = null): Response
    {
        if($season === null)
        {
            // Pour renvoyer vers une 404 (non existante pour le moment -> renvoie une erreur symfony)
            // throw $this->createNotFoundException('Ce film n\'existe pas');

            $this->addFlash(
                'info',
                ["message" => 'Cette saison n\'existe pas dans la base de données.',
                "title" => ""]
            );

            return $this->redirectToRoute('app_back_season_index');

        }
        return $this->render('back/season/show.html.twig', [
            'season' => $season,
            'movie' => $season->getMovie()
        ]);
    }

    #[Route('/{id<\d+>}/edit/{idSeason<\d+>}', name: 'app_back_season_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, /* #[MapEntity(id: "id")] */ Movie $movie, #[MapEntity(id: "idSeason")] Season $season = null, EntityManagerInterface $entityManager): Response
    {
        if($season === null)
        {
            // Pour renvoyer vers une 404 (non existante pour le moment -> renvoie une erreur symfony)
            // throw $this->createNotFoundException('Ce film n\'existe pas');

            $this->addFlash(
                'info',
                ["message" => 'Cette saison n\'existe pas dans la base de données.',
                "title" => ""]
            );

            // return $this->redirectToRoute('app_back_season_index');

        }
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_back_season_index', ["id" => $season->getMovie()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/season/edit.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_season_delete', methods: ['POST'])]
    public function delete(Request $request, Season $season = null, EntityManagerInterface $entityManager): Response
    {
        if($season === null)
        {
            // Pour renvoyer vers une 404 (non existante pour le moment -> renvoie une erreur symfony)
            // throw $this->createNotFoundException('Ce film n\'existe pas');

            $this->addFlash(
                'info',
                ["message" => 'Cette saison n\'existe pas dans la base de données.',
                "title" => ""]
            );

            return $this->redirectToRoute('app_back_season_index');

        }
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
            $entityManager->remove($season);
            $entityManager->flush();
        }

        $this->addFlash(
            'info',
            ["message" => 'La saison a bien été supprimé',
            "title" => ""]
        );

        return $this->redirectToRoute('app_back_season_index', ["id" => $season->getMovie()->getId() ], Response::HTTP_SEE_OTHER);
    }
}
