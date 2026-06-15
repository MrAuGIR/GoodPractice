<?php

namespace App\Command;

use App\Import\BonnesPratiquesImporter;
use App\Import\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:bonnes-pratiques',
    description: 'Importe un corpus de bonnes pratiques (catégories + articles) depuis un fichier JSON.',
)]
class ImportBonnesPratiquesCommand extends Command
{
    public function __construct(private BonnesPratiquesImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Chemin du fichier JSON à importer')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simule l\'import sans rien écrire en base');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');
        $dryRun = (bool) $input->getOption('dry-run');

        if (!is_file($file) || !is_readable($file)) {
            $io->error(sprintf('Fichier introuvable ou illisible : %s', $file));

            return Command::FAILURE;
        }

        $json = file_get_contents($file);
        if (false === $json) {
            $io->error('Impossible de lire le fichier.');

            return Command::FAILURE;
        }

        $io->title('Import des bonnes pratiques'.($dryRun ? ' (dry-run)' : ''));

        try {
            $result = $this->importer->importFromJson($json, $dryRun);
        } catch (ImportException $e) {
            $io->error('Import refusé :');
            $io->listing($e->getErrors());

            return Command::FAILURE;
        }

        $io->definitionList(
            ['Catégories créées' => $result->categoriesCreated],
            ['Articles créés' => $result->articlesCreated],
            ['Articles mis à jour' => $result->articlesUpdated],
            ['Articles ignorés (doublons)' => $result->articlesSkipped],
        );

        if ($dryRun) {
            $io->note('Dry-run : aucune écriture en base.');
        } else {
            $io->success('Import terminé.');
        }

        return Command::SUCCESS;
    }
}
