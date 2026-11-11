<?php

namespace App\Tests\Api;

class ImportBonnesPratiquesControllerTest extends ApiTestCase
{
    private function import(string $body, ?string $token, string $query = ''): void
    {
        $server = ['CONTENT_TYPE' => 'application/json'];
        if (null !== $token) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer '.$token;
        }
        $this->client->request('POST', '/api/import/bonnes-pratiques'.$query, server: $server, content: $body);
    }

    public function testEditorIsForbidden(): void
    {
        $this->import('{}', $this->login('editor@goodpractice.test'));

        self::assertResponseStatusCodeSame(403);
    }

    public function testEmptyBodyIsBadRequest(): void
    {
        $this->import('  ', $this->login('admin@goodpractice.test'));

        self::assertResponseStatusCodeSame(400);
    }

    public function testInvalidJsonIsUnprocessable(): void
    {
        $this->import('{pas du json', $this->login('admin@goodpractice.test'));

        self::assertResponseStatusCodeSame(422);
        self::assertNotEmpty($this->responseData()['errors']);
    }

    public function testDryRunImport(): void
    {
        $json = (string) file_get_contents(\dirname(__DIR__, 2).'/data/bonnes-pratiques.json');

        $this->import($json, $this->login('admin@goodpractice.test'), '?dry-run=1');

        self::assertResponseIsSuccessful();
        self::assertTrue($this->responseData()['dryRun']);
    }
}
