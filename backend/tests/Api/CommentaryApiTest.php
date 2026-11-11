<?php

namespace App\Tests\Api;

class CommentaryApiTest extends ApiTestCase
{
    public function testAnonymousCannotComment(): void
    {
        $this->jsonRequest('POST', '/api/commentaries', [
            'title' => 'Anonyme',
            'description' => 'Pas connecté.',
            'article' => $this->firstArticleIri(),
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testUserCommentsAndAuthorIsAssigned(): void
    {
        $token = $this->login('user@goodpractice.test');
        $iri = $this->postComment($token);

        $this->jsonRequest('GET', $iri);
        self::assertResponseIsSuccessful();
        self::assertSame('Lecteur', $this->responseData()['author']['name']);
    }

    public function testOnlyAuthorOrAdminCanDelete(): void
    {
        $iri = $this->postComment($this->login('user@goodpractice.test'));

        $this->jsonRequest('DELETE', $iri, token: $this->login('editor@goodpractice.test'));
        self::assertResponseStatusCodeSame(403);

        $this->jsonRequest('DELETE', $iri, token: $this->login('user@goodpractice.test'));
        self::assertResponseStatusCodeSame(204);
    }

    public function testAdminCanDeleteAnyComment(): void
    {
        $iri = $this->postComment($this->login('user@goodpractice.test'));

        $this->jsonRequest('DELETE', $iri, token: $this->login('admin@goodpractice.test'));
        self::assertResponseStatusCodeSame(204);
    }

    private function postComment(string $token): string
    {
        $this->jsonRequest('POST', '/api/commentaries', [
            'title' => 'Mon avis',
            'description' => 'Très utile, merci.',
            'article' => $this->firstArticleIri(),
        ], $token);
        self::assertResponseStatusCodeSame(201);

        return $this->responseData()['@id'];
    }

    private function firstArticleIri(): string
    {
        $this->jsonRequest('GET', '/api/articles?itemsPerPage=1');

        return $this->responseData()['member'][0]['@id'];
    }
}
