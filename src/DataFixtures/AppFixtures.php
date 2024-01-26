<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Review;
use App\Entity\Season;
use App\Entity\Casting;
use App\Repository\MovieRepository;
use App\Repository\ReviewRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


class AppFixtures extends Fixture
{

    private $genres = [];
    private $persons = [];

    public function __construct(
        private ReviewRepository $reviewRepository,
        private SluggerInterface $slugger,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create();
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Movie($faker));
        $faker->addProvider(new \Xylis\FakerCinema\Provider\TvShow($faker));
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Person($faker));
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Character($faker));
        $faker->addProvider(new \Smknstd\FakerPicsumImages\FakerPicsumImagesProvider($faker));

        for ($i = 1; $i < $faker->numberBetween(1, 20); $i++)
        {
            $genre = new Genre;
            $genre->setName($faker->unique()->movieGenre());

            $manager->persist($genre);

            $this->genres[] = $genre;
        }

        for ($i = 1; $i <= $faker->randomDigitNotNull(); $i++)
        {
            $personFullName = explode(" ",$faker->unique()->actor());

            $person = new Person;
            $person->setFirstname($personFullName[0]);
            $person->setLastname($personFullName[1]);

            $manager->persist($person);

            $this->persons[] = $person;
        }

        for ($i = 1; $i <= 50; $i++)
        {
            $movie = new Movie;
            // si un movie est une série alors il y a des saison
            if ($faker->boolean()) 
            {
                // c'est un film
                $movie->setTitle($faker->unique()->movie());
                $movie->setSlug($this->slugger->slug($movie->getTitle()));
                $movie->setType('Film');
                $movie->setDuration(random_int(80, 330));
            }
            else
            {
                // c'est une série
                $movie->setTitle($faker->unique()->tvShow());
                $movie->setSlug($this->slugger->slug($movie->getTitle()));
                $movie->setType('Série');
                $movie->setDuration(random_int(25, 60));
                // il y a aussi des saisons
                for ($j = 1; $j < random_int(2, 12); $j++) {
                    $season = new Season;
                    $season->setNumber($j);
                    $season->setEpisodesNumber(random_int(6, 12));
                    $season->setMovie($movie);
                    $manager->persist($season);
                }
            }

            $movie->setReleaseDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween()));
            $movie->setSummary($faker->realText(60));
            $movie->setSynopsis($faker->optional(0.9)->realText());
            $movie->setPoster($faker->optional(0.9)->imageUrl(200, 300, $randomize = true));
            $movie->setRating($faker->optional(0.8)->randomFloat(1, 1, 5));

            for ($j=0; $j < random_int(0, 5); $j++) { 
                $movie->addGenre($faker->unique()->randomElement($this->genres)); // Première façon de faire (la meilleur) : seconde  méthode avec Person ci-dessous
            }

            $orders = range(0, random_int(0, 10));
            shuffle($orders);
            if (count($orders) != 1) {
                foreach ($orders as $order) {
                    $casting = new Casting;
                    $casting->setMovie($movie);
                    $casting->setPerson($this->persons[random_int(0, count($this->persons) - 1)]); // Seconde façon de faire
                    $casting->setCastingOrder($order+1);
                    $casting->setRole($faker->character());
                    $manager->persist($casting);
                }
            }

            // on crée entre 0 et 5 critiques (Reviews)
            for ($j = 0; $j < random_int(0, 6); $j++) {
                $review = new Review();
                $review->setMovie($movie);
                $review->setUsername($faker->name());
                $review->setEmail($faker->email());
                $review->setContent(($faker->realTextBetween()));
                $review->setRating(random_int(1, 5));
                $review->setWatchedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisDecade()));
                $reactions = ['smile', 'cry', 'think', 'sleep', 'dream',];
                shuffle($reactions);
                $review->setReactions(array_slice($reactions, 0, random_int(0, 5)));
                $manager->persist($review);
            }

            $manager->persist($movie);
            // on doit flusher pour pouvoir calculer la moyenne du film
            $manager->flush();
            // Calcul du nouveau rating du film
            $averageRating = $this->reviewRepository->averageRating($movie);
            $movie->setRating($averageRating);


            $manager->persist($movie);

        }

        $userRoles = [
            "admin" => [
                "email" => "admin@admin.com",
                "password" => "$2y$13\$EsPORWxMkHvR.QvbJe12b.qY5iPyU49XVIvKbigrTU.qguJWi7gQW",
                "role" => "ROLE_ADMIN"            
            ],
            "user" => [
                "email" => "user@user.com",
                "password" => "$2y$13\$MkFHwRbfO1afnPOn.oFlUeyo085zjqV3yEpiL/6N9oUWyCS.IFxvu",
                "role" => "ROLE_USER"            
            ],
            "manager" => [
                "email" => "manager@manager.com",
                "password" => "$2y$13\$Z2ueSB9eDxm11ExiiMCqEe7MADnVJs0P15vDb.u.XBo5kVveAgtSK",
                "role" => "ROLE_MANAGER"            
            ],
        ];

        foreach($userRoles as $userFixture)
        {
            $user = new User;
            $user->setEmail($userFixture["email"]);
            $user->setPassword($userFixture["password"]);
            $user->setRoles([$userFixture["role"]]);

            $manager->persist($user);
        }

        $manager->flush();

        // for ($i = 1; $i < $faker->numberBetween(1, 5); $i++)
        // {
        //     $season = new Season;
        //     $season->setNumber($i);
        //     $season->setEpisodesNumber($faker->numberBetween(7, 15));
        //     $season->setMovie($serie->getId());
        //     $manager->persist($season);
        //     $manager->flush();
        //     $this->addReference(self::SEASON_REFERENCE, $season);
        // }
    }
}
