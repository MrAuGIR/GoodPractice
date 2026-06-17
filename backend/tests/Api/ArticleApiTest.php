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

    private function firstCategoryIri(): string
    {
        $this->jsonRequest('GET', '/api/categories?itemsPerPage=1');

        return $this->responseData()['member'][0]['@id'];
    }
}
