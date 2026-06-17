<?php

namespace App\Tests\Api;

class TagApiTest extends ApiTestCase
{
    public function testCollectionIsPublic(): void
    {
        $this->jsonRequest('GET', '/api/tags');

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('member', $this->responseData());
    }

    public function testUserCannotCreateTag(): void
    {
        $token = $this->login('user@goodpractice.test');
        $this->jsonRequest('POST', '/api/tags', ['name' => 'Refusé'], $token);

        self::assertResponseStatusCodeSame(403);
    }

    public function testEditorCanCreateTagWithGeneratedSlug(): void
    {
        $token = $this->login('editor@goodpractice.test');
        $this->jsonRequest('POST', '/api/tags', ['name' => 'Nouveau Tag '.uniqid()], $token);

        self::assertResponseStatusCodeSame(201);
        self::assertNotEmpty($this->responseData()['slug']);
    }
}
