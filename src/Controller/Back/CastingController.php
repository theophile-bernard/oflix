<?php

namespace App\Controller\Back;

use App\Entity\Casting;
use App\Form\CastingType;
use App\Repository\CastingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/casting')]
class CastingController extends AbstractController
{
    #[Route('/', name: 'app_back_casting_index', methods: ['GET'])]
    public function index(CastingRepository $castingRepository): Response
    {
        return $this->render('back/casting/index.html.twig', [
            'castings' => $castingRepository->findAllOrderByMovieAscQB(),
        ]);
    }

    #[Route('/new', name: 'app_back_casting_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $casting = new Casting();
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($casting);
            $entityManager->flush();

            return $this->redirectToRoute('app_back_casting_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/casting/new.html.twig', [
            'casting' => $casting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_casting_show', methods: ['GET'])]
    public function show(Casting $casting = null): Response
    {
        if($casting === null)
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

        return $this->render('back/casting/show.html.twig', [
            'casting' => $casting,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_back_casting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Casting $casting = null, EntityManagerInterface $entityManager): Response
    {
        if($casting === null)
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

        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_back_casting_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/casting/edit.html.twig', [
            'casting' => $casting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_casting_delete', methods: ['POST'])]
    public function delete(Request $request, Casting $casting = null, EntityManagerInterface $entityManager): Response
    {
        if($casting === null)
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
        
        if ($this->isCsrfTokenValid('delete'.$casting->getId(), $request->request->get('_token'))) {
            $entityManager->remove($casting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_back_casting_index', [], Response::HTTP_SEE_OTHER);
    }
}
