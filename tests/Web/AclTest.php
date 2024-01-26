<?php

namespace App\Tests\Web;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AclTest extends WebTestCase
{
    public function testHome(): void
    {
        // on crée un instance d'un navigateur
        $client = static::createClient();
        // on envoie une requête
        $crawler = $client->request('GET', '/');

        // on teste si la route répond bien
        $this->assertResponseIsSuccessful();
        // on teste si le h1 de la page contient ce que le client a demandé
        $this->assertSelectorTextContains('h1', 'Films, séries TV et popcorn en illimité.');
    }

    /**
     * @dataProvider urlNotConnectedProvider
     */
    public function testNotConnected($url, $codeRetour): void
    {
        // on crée un instance d'un navigateur
        $client = static::createClient();
        // on envoie une requête
        $crawler = $client->request('GET', $url);

        $this->assertResponseStatusCodeSame($codeRetour);
    }

    // Utilisation d'un dataprovider
    // REFER : https://docs.phpunit.de/en/10.5/writing-tests-for-phpunit.html#data-providers
    public static function urlNotConnectedProvider(): array
    {
        return [
            ['/', 200],
            ['/movies', 200],
            ['/show/Adventure-Time', 200],
        ];
    }

    // Utilisation d'un dataprovider
    // REFER : https://docs.phpunit.de/en/10.5/writing-tests-for-phpunit.html#data-providers
    public static function urlConnectedProvider(): array
    {
        return [
            ['/',                       200, 'GET', 'manager@manager.com'],
            ['/movies',                 200, 'GET', 'manager@manager.com'],
            ['/show/Adventure-Time',    200, 'GET', 'manager@manager.com'],
            ['/back/movie',             301, 'GET', 'manager@manager.com'],
            ['/back/movie/new',         403, 'GET', 'user@user.com'],
            ['/back/movie/new',         403, 'GET', 'manager@manager.com'],
            ['/back/movie/new',         200, 'GET', 'admin@admin.com'],
        ];
    }

    // REFER : https://symfony.com/blog/new-in-symfony-5-1-simpler-login-in-tests
    /**
     * @dataProvider urlConnectedProvider
     */
    public function testStatusCodeConnected($url, $expectedStatusCode, $method, $user): void
    {
        // création d'un faux navigateur
        $client = static::createClient();

        // connecter un utilisateur
        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        // On n'est pas dans un service de Symfont, on ne peut donc pas injecter de dépendances
        // On doit donc récupérer le conteneur de service
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail($user);

        $client->loginUser($testUser);

        // Exécution d'une requête
        $crawler = $client->request($method, $url);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }


    public static function urlSlugs(): array
    {
        return [
        ['user@user.com', 'Stand-by-Me', 302, 5],
        ['user@user.com', 'The-Good-Place', 422, ''],
        ['user@user.com', 'The-Blacklist', 422, ''],
        ['user@user.com', 'Californication', 302, 5],
        ];
    }


    /**
     * @dataProvider urlSlugs
     */
    public function testAddReview($user, $slug, $expectedStatusCode, $rating)
    {
        $client = static::createClient();

        // connecter un utilisateur
        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        // On n'est pas dans un service de Symfont, on ne peut donc pas injecter de dépendances
        // On doit donc récupérer le conteneur de service
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail($user);

        $client->loginUser($testUser);
        $client->request('GET', "/show/{$slug}/add-review");

        $crawler = $client->submitForm('Envoyer', [
            'review[username]' => 'test',
            'review[email]'    => 'test@test.com',
            'review[content]'  => 'blablablablavbla',
            'review[rating]'   => $rating,
            'review[reactions]'    => ["smile", "cry"],
            'review[watchedAt]'    => '2009-06-15 13:45:30',
        ]);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    public static function urlFavorites(): array
    {
        return [
        ['user@user.com', 'Stand-by-Me', 302],
        ['user@user.com', 'The-Good-Place', 302],
        ['user@user.com', 'Stand-by-Me', 302],
        ['user@user.com', 'Californication', 302],
        ];
    }


    /**
     * @dataProvider urlFavorites
     */
    public function testFavorites($user, $slug, $expectedStatusCode)
    {
        $client = static::createClient();

        // connecter un utilisateur
        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        // On n'est pas dans un service de Symfont, on ne peut donc pas injecter de dépendances
        // On doit donc récupérer le conteneur de service
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail($user);

        $client->loginUser($testUser);
        $client->request('GET', "/show/{$slug}");

        $crawler = $client->submitForm("favorite");
        
        $client->request('GET', "/favorites");

        $crawler = $client->submitForm("favorite");

        $client->request('GET', "/show/{$slug}");

        $crawler = $client->submitForm("favorite");

        $client->request('GET', "/favorites");

        $crawler = $client->clickLink("Vider la liste");

        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    // Tests du prof :

    /**
     * @dataProvider urlReviewProf
     */
    public function testAddReviewProf($slug, $userName, $returnGet, $returnPost)
    {
        // on instancie un client
        $client = static::createClient();

        // connecter un utilisateur
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user@user.com');
        $client->loginUser($testUser);

        // on appelle la page d'ajout d'une revue
        $crawler = $client->request('GET', "/show/{$slug}/add-review");

        // on vérifie qu'on est bien sur la page du formulaire d'ajout
        $this->assertResponseStatusCodeSame($returnGet);

        // vérifier le code de réponse
        if ($client->getResponse()->getStatusCode() !== 200) {
            // Si le code de réponse n'est pas 200, arrêter le traitement
            return;
        }
        // on rempli le formulaire
        $crawler = $client->submitForm('Envoyer', [
            'review[username]'      => $userName,
            'review[email]'         => 'Patrick@patrick.com',
            'review[content]'       => 'La revue de Patrick',
            'review[rating]'        => '5',
            'review[reactions]'     => ['smile', 'cry'],
            'review[watchedAt]'     => '2024-01-26 10:19:00',
        ]);

        // on fois soumis, on attend un retour 302
        $this->assertResponseStatusCodeSame($returnPost);
    }

    public static function urlReviewProf()
    {
        return [
            ['The-Good-Place', 'Patrick', 200, 302],
            ['The-Blacklist', '', 200, 422],
        ];
    }
}