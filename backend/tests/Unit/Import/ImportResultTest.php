<?php

namespace App\Tests\Unit\Import;

use App\Import\ImportException;
use App\Import\ImportResult;
use PHPUnit\Framework\TestCase;

class ImportResultTest extends TestCase
{
    public function testToArrayExposesAllCounters(): void
    {
        $result = new ImportResult();
        $result->categoriesCreated = 1;
        $result->tagsCreated = 2;
        $result->articlesCreated = 3;
        $result->articlesUpdated = 4;
        $result->articlesSkipped = 5;
        $result->dryRun = true;

        self::assertSame([
            'dryRun' => true,
            'categoriesCreated' => 1,
            'tagsCreated' => 2,
            'articlesCreated' => 3,
            'articlesUpdated' => 4,
            'articlesSkipped' => 5,
        ], $result->toArray());
    }

    public function testImportExceptionKeepsErrorList(): void
    {
        $e = new ImportException(['erreur 1', 'erreur 2']);

        self::assertSame(['erreur 1', 'erreur 2'], $e->getErrors());
        self::assertSame('erreur 1 | erreur 2', $e->getMessage());
    }
}
