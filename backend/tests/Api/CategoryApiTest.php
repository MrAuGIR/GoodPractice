<?php

namespace App\Tests\Api;

class CategoryApiTest extends ApiTestCase
{
    public function testCollectionIsPublic(): void
    {
        $this->jsonRequest('GET', '/api/categories');

        self::assertResponseIsSuccessful();
        self::assertGreaterThan(0, $this->responseData()['totalItems']);
    }

    public function testEditorCannotCreate(): void
    {
        $this->jsonRequest('POST', '/api/categories', ['name' => 'Interdite'], $this->login('editor@goodpractice.test'));

        self::assertResponseStatusCodeSame(403);
    }

    public function testAdminCrud(): void
    {
        $token = $this->login('admin@goodpractice.test');

        $this->jsonRequest('POST', '/api/categories', ['name' => 'Catégorie '.uniqid()], $token);
        self::assertResponseStatusCodeSame(201);
        $iri = $this->responseData()['@id'];

        $this->jsonRequest('PUT', $iri, ['name' => 'Renommée', 'defaultImage' => 'https://exemple.test/a.png'], $token);
        self::assertResponseIsSuccessful();
        self::assertSame('Renommée', $this->responseData()['name']);

        $this->jsonRequest('DELETE', $iri, token: $token);
        self::assertResponseStatusCodeSame(204);
    }

    public function testValidationRejectsBlankName(): void
    {
        $this->jsonRequest('POST', '/api/categories', ['name' => ''], $this->login('admin@goodpractice.test'));

        self::assertResponseStatusCodeSame(422);
    }
}
