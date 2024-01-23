<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbApi
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apikey
    ) {}

    public function fetchPoster(string $title)
    {
        // Appelle l'API de OpenMovieDataBase (ONDB)
        // On récupère le JSON 
        // Récupérer l'entrée Poster du json
        // vérifier si cette entrée existe

        $response = $this->client->request(
            'GET',
            'http://www.omdbapi.com/', [
                // these values are automatically encoded before including them in the URL
                'query' => [
                    'apikey' => $this->apikey,
                    't' => $title,
            ],
        ]);

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        if($statusCode !== 200)
        {
            return null;
        }
        // récupération du JSON sous forme de tableau
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        if(!isset($content["Poster"]))
        {
            return null;
        }
        
        return $content["Poster"];

    }
}