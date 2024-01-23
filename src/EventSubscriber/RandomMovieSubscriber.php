<?php

namespace App\EventSubscriber;

use App\Repository\MovieRepository;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class RandomMovieSubscriber implements EventSubscriberInterface
{
    
    public function __construct(
        private MovieRepository $movieRepository,
        private Environment $twig,
    ) 
    {}
    
    public function onKernelController(ControllerEvent $event): void
    {
        // on veut récupérer un film au hasard
        // Soit avec un findAll, puis un au hasard (pas efficace mais simple à mettre en oeuvre)
        // Soit avec une requête pesonnalisée (mieux mais plus de tavail)

        // $movies = $this->movieRepository->findAll();
        // shuffle permet de mélanger le tableau au hasard, il mélange le tableau sur lui même
        // shuffle($movies);
        // on prend le premier élément => hasard
        // $randomMovie = $movies[0];

        $randomMovie = $this->movieRepository->findOneByRandom();

        // on met le $randomMovie à disposition des twigs uniquement, pas au php
        $this->twig->addGlobal('randomMovie', $randomMovie);

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
