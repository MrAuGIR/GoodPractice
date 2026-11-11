<?php

namespace App\Tests\Integration\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ImportBonnesPratiquesCommandTest extends KernelTestCase
{
    private CommandTester $tester;
    private ?string $tmpFile = null;

    protected function setUp(): void
    {
        $application = new Application(self::bootKernel());
        $this->tester = new CommandTester($application->find('app:import:bonnes-pratiques'));
    }

    protected function tearDown(): void
    {
        if (null !== $this->tmpFile && is_file($this->tmpFile)) {
            unlink($this->tmpFile);
        }
        parent::tearDown();
    }

    private function writeJson(string $content): string
    {
        $this->tmpFile = (string) tempnam(sys_get_temp_dir(), 'import');
        file_put_contents($this->tmpFile, $content);

        return $this->tmpFile;
    }

    public function testMissingFileFails(): void
    {
        $status = $this->tester->execute(['file' => '/inexistant/fichier.json']);

        self::assertSame(Command::FAILURE, $status);
        self::assertStringContainsString('Fichier introuvable', $this->tester->getDisplay());
    }

    public function testInvalidJsonIsRefused(): void
    {
        $status = $this->tester->execute(['file' => $this->writeJson('{pas du json')]);

        self::assertSame(Command::FAILURE, $status);
        self::assertStringContainsString('Import refusé', $this->tester->getDisplay());
    }

    public function testDryRunImport(): void
    {
        $file = $this->writeJson(json_encode([
            'categories' => [['name' => 'Catégorie CLI', 'defaultImage' => null]],
            'articles' => [[
                'title' => 'Article importé en CLI',
                'category' => 'Catégorie CLI',
                'description' => 'Description suffisamment longue pour passer la validation.',
                'url' => null,
                'urlImg' => null,
                'tags' => ['CliTag'],
                'featured' => false,
            ]],
        ], \JSON_THROW_ON_ERROR));

        $status = $this->tester->execute(['file' => $file, '--dry-run' => true]);

        self::assertSame(Command::SUCCESS, $status);
        self::assertStringContainsString('dry-run', $this->tester->getDisplay());
    }

    public function testRealImportOfShippedCorpus(): void
    {
        $status = $this->tester->execute(['file' => \dirname(__DIR__, 3).'/data/bonnes-pratiques.json']);

        self::assertSame(Command::SUCCESS, $status, $this->tester->getDisplay());
        self::assertStringContainsString('Import terminé', $this->tester->getDisplay());
    }
}
