<?php

namespace App\Tests\Api;

class AuthApiTest extends ApiTestCase
{
    public function testLoginReturnsJwtAndRefreshToken(): void
    {
        $this->client->request('POST', '/api/login_check', server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['email' => 'user@goodpractice.test', 'password' => 'password']));

        self::assertResponseIsSuccessful();
        $data = $this->responseData();
        self::assertNotEmpty($data['token']);
        self::assertNotEmpty($data['refresh_token']);
    }

    public function testLoginWithWrongPasswordFails(): void
    {
        $this->client->request('POST', '/api/login_check', server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['email' => 'user@goodpractice.test', 'password' => 'mauvais']));

        self::assertResponseStatusCodeSame(401);
    }

    public function testRefreshTokenIsSingleUse(): void
    {
        $this->client->request('POST', '/api/login_check', server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['email' => 'user@goodpractice.test', 'password' => 'password']));
        $refresh = $this->responseData()['refresh_token'];

        $this->client->request('POST', '/api/token/refresh', server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['refresh_token' => $refresh]));
        self::assertResponseIsSuccessful();
        self::assertNotEmpty($this->responseData()['token']);

        $this->client->request('POST', '/api/token/refresh', server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['refresh_token' => $refresh]));
        self::assertResponseStatusCodeSame(401);
    }
}
