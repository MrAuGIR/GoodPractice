<?php

namespace App\Tests\Unit\ExternalApi\OpenGraph;

use App\ExternalApi\OpenGraph\OpenGraphData;
use PHPUnit\Framework\TestCase;

class OpenGraphDataTest extends TestCase
{
    public function testToArrayExposesAllFields(): void
    {
        $data = new OpenGraphData('Titre', 'Description', 'https://img', 'Site', 'https://url');

        self::assertSame([
            'title' => 'Titre',
            'description' => 'Description',
            'image' => 'https://img',
            'siteName' => 'Site',
            'url' => 'https://url',
        ], $data->toArray());
    }

    public function testFieldsDefaultToNull(): void
    {
        self::assertSame([
            'title' => null,
            'description' => null,
            'image' => null,
            'siteName' => null,
            'url' => null,
        ], (new OpenGraphData())->toArray());
    }
}
