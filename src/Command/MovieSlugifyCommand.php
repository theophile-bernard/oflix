<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Service\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'movie:slugify',
    description: 'Création des slugs pour les titres en base',
)]
class MovieSlugifyCommand extends Command
{
    public function __construct(
        private MovieRepository $movieRepository,
        private MySlugger $slugger,
        private EntityManagerInterface $em,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
        // dans cette partie, on rentre la logique de notre commande

        // on veut "slugifier" les titres des films en base de donnée
        // Récupérer ces titres
        $movies = $this->movieRepository->findAll();
        // parcourir les movies
        foreach ($movies as $movie) {
            // leur appliquer le slugify
            $movie->setSlug($this->slugger->slugTitle($movie->getTitle()));
            // on persiste le movie
            $this->em->persist($movie);
        }
        // les sauvegarder
        $this->em->flush();

        $io->success('La mise à jour des slugs est terminée');

        return Command::SUCCESS;
    }
}
