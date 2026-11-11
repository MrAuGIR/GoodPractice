<?php

namespace App\Tests\Api;

class ArticleApiTest extends ApiTestCase
{
    public function testCollectionIsPublic(): void
    {
        $this->jsonRequest('GET', '/api/articles');

        self::assertResponseIsSuccessful();
        $data = $this->responseData();
        self::assertArrayHasKey('member', $data);
        self::assertArrayHasKey('totalItems', $data);
    }

    public function testAnonymousCannotCreate(): void
    {
        $this->jsonRequest('POST', '/api/articles', [
            'title' => 'Anonyme',
            'description' => 'Tentative anonyme de création.',
            'category' => $this->firstCategoryIri(),
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testUserCannotCreate(): void
    {
        $token = $this->login('user@goodpractice.test');

        $this->jsonRequest('POST', '/api/articles', [
            'title' => 'Par un lecteur',
            'description' => 'Un simple lecteur ne peut pas créer.',
            'category' => $this->firstCategoryIri(),
        ], $token);

        self::assertResponseStatusCodeSame(403);
    }

    public function testAdminCanCreate(): void
    {
        $token = $this->login('admin@goodpractice.test');

        $this->jsonRequest('POST', '/api/articles', [
            'title' => 'Créé par admin '.uniqid(),
            'description' => 'Article créé via test fonctionnel.',
            'category' => $this->firstCategoryIri(),
            'featured' => true,
        ], $token);

        self::assertResponseStatusCodeSame(201);
        self::assertTrue($this->responseData()['featured']);
    }

    public function testOnlyAuthorOrAdminCanEditAndDelete(): void
    {
        $editorToken = $this->login('editor@goodpractice.test');
        $this->jsonRequest('POST', '/api/articles', [
            'title' => 'Article éditeur '.uniqid(),
            'description' => 'Rédigé par l\'éditeur.',
            'category' => $this->firstCategoryIri(),
        ], $editorToken);
        self::assertResponseStatusCodeSame(201);
        $iri = $this->responseData()['@id'];
        self::assertSame('Éditeur', $this->responseData()['author']['name']);

        $payload = [
            'title' => 'Titre modifié',
            'description' => 'Description modifiée.',
            'category' => $this->firstCategoryIri(),
        ];

        $this->jsonRequest('PUT', $iri, $payload, $this->login('user@goodpractice.test'));
        self::assertResponseStatusCodeSame(403);

        $this->jsonRequest('PUT', $iri, $payload, $editorToken);
        self::assertResponseIsSuccessful();
        self::assertSame('Titre modifié', $this->responseData()['title']);

        $this->jsonRequest('DELETE', $iri, token: $this->login('admin@goodpractice.test'));
        self::assertResponseStatusCodeSame(204);
    }

    private function firstCategoryIri(): string
    {
        $this->jsonRequest('GET', '/api/categories?itemsPerPage=1');

        return $this->responseData()['member'][0]['@id'];
    }
}
