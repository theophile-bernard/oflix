<?php

namespace App\EventListener;

use App\Entity\Review;
use Doctrine\ORM\Events;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

// REFER : https://symfony.com/doc/6.4/doctrine/events.html#doctrine-lifecycle-listeners
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Review::class)]
class ReviewInsertUpdater
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private EntityManagerInterface $entityManager
    )
    {}
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function postPersist(Review $review, PostPersistEventArgs $event): void
    {
        // on veut mettre à jour le rating
        // besoin du review Repository
        // le Movie est donné par le Review
        $movie = $review->getMovie();
        // calcul de la moyenne des notes
        $rating = $this->reviewRepository->averageRating($movie);
        // on la sauvegarde
        $movie->setRating($rating);
        $this->entityManager->flush();
    }
}