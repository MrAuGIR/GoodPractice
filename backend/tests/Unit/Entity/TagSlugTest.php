<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

class TagSlugTest extends TestCase
{
    public function testSlugIsGeneratedFromName(): void
    {
        $tag = (new Tag())->setName('Frontend');
        $tag->computeSlug();

        self::assertSame('frontend', $tag->getSlug());
    }

    public function testSlugIsAsciiAndLowercased(): void
    {
        $tag = (new Tag())->setName('Accessibilité');
        $tag->computeSlug();

        self::assertSame('accessibilite', $tag->getSlug());
    }

    public function testSlugHandlesSpacesAndCase(): void
    {
        $tag = (new Tag())->setName('Green IT');
        $tag->computeSlug();

        self::assertSame('green-it', $tag->getSlug());
    }

    public function testExplicitSlugIsPreserved(): void
    {
        $tag = (new Tag())->setName('Frontend')->setSlug('custom-slug');
        $tag->computeSlug();

        self::assertSame('custom-slug', $tag->getSlug());
    }
}
