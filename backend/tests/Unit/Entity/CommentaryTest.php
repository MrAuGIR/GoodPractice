<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Article;
use App\Entity\Commentary;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentaryTest extends TestCase
{
    public function testDatePostedIsSetOnCreation(): void
    {
        $before = new \DateTimeImmutable();
        $comment = new Commentary();

        self::assertNotNull($comment->getDatePosted());
        self::assertGreaterThanOrEqual($before, $comment->getDatePosted());
        self::assertNull($comment->getId());
    }

    public function testVotesCannotBeNegative(): void
    {
        $comment = (new Commentary())->setApproved(-3)->setDisapproved(-1);

        self::assertSame(0, $comment->getApproved());
        self::assertSame(0, $comment->getDisapproved());

        $comment->setApproved(4)->setDisapproved(2);

        self::assertSame(4, $comment->getApproved());
        self::assertSame(2, $comment->getDisapproved());
    }

    public function testAccessors(): void
    {
        $article = new Article();
        $author = new User();
        $date = new \DateTimeImmutable('2024-01-01');

        $comment = (new Commentary())
            ->setTitle('Titre')
            ->setDescription('Contenu')
            ->setDatePosted($date)
            ->setArticle($article)
            ->setAuthor($author);

        self::assertSame('Titre', $comment->getTitle());
        self::assertSame('Contenu', $comment->getDescription());
        self::assertSame($date, $comment->getDatePosted());
        self::assertSame($article, $comment->getArticle());
        self::assertSame($author, $comment->getAuthor());
    }
}
