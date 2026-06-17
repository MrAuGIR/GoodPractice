<?php

namespace App\Tests\Integration\Import;

use App\Import\BonnesPratiquesImporter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BonnesPratiquesImporterTest extends KernelTestCase
{
    private const JSON = <<<'JSON'
        {
          "categories": [{ "name": "Catégorie Test", "defaultImage": null }],
          "articles": [{
            "title": "Article de test importé",
            "category": "Catégorie Test",
            "description": "Description de test suffisamment longue pour être valide.",
            "url": null,
            "urlImg": null,
            "tags": ["TagAlpha", "TagBeta"],
            "featured": true
          }]
        }
        JSON;

    private function importer(): BonnesPratiquesImporter
    {
        self::bootKernel();

        return self::getContainer()->get(BonnesPratiquesImporter::class);
    }

    public function testImportCreatesCategoryTagsAndArticle(): void
    {
        $result = $this->importer()->importFromJson(self::JSON);

        self::assertSame(1, $result->categoriesCreated);
        self::assertSame(2, $result->tagsCreated);
        self::assertSame(1, $result->articlesCreated);
        self::assertSame(0, $result->articlesUpdated);
    }

    public function testReimportIsIdempotent(): void
    {
        $importer = $this->importer();
        $importer->importFromJson(self::JSON);

        $result = $importer->importFromJson(self::JSON);

        self::assertSame(0, $result->categoriesCreated);
        self::assertSame(0, $result->tagsCreated);
        self::assertSame(0, $result->articlesCreated);
        self::assertSame(1, $result->articlesUpdated);
    }

    public function testDryRunWritesNothing(): void
    {
        $result = $this->importer()->importFromJson(self::JSON, dryRun: true);

        self::assertTrue($result->dryRun);
        self::assertSame(1, $result->articlesCreated);
    }
}
