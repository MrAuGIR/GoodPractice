<?php

namespace App\Tests\Api;

class EnrichOpenGraphControllerTest extends ApiTestCase
{
    private function enrich(mixed $body, ?string $token): void
    {
        $server = ['CONTENT_TYPE' => 'application/json'];
        if (null !== $token) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer '.$token;
        }
        $this->client->request('POST', '/api/enrich/opengraph', server: $server, content: json_encode($body));
    }

    public function testAnonymousIsRejected(): void
    {
        $this->enrich(['url' => 'https://exemple.test'], null);

        self::assertResponseStatusCodeSame(401);
    }

    public function testSimpleUserIsForbidden(): void
    {
        $this->enrich(['url' => 'https://exemple.test'], $this->login('user@goodpractice.test'));

        self::assertResponseStatusCodeSame(403);
    }

    public function testMissingUrlIsBadRequest(): void
    {
        $this->enrich(['foo' => 'bar'], $this->login('editor@goodpractice.test'));

        self::assertResponseStatusCodeSame(400);
    }

    public function testInvalidUrlIsUnprocessable(): void
    {
        // Rejetée par OpenGraphFetcher avant tout appel réseau
        $this->enrich(['url' => 'ftp://exemple.test/fichier'], $this->login('editor@goodpractice.test'));

        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString('URL invalide', $this->responseData()['error']);
    }
}
