<?php

namespace App\Tests\Unit\ExternalApi\OpenGraph;

use App\ExternalApi\OpenGraph\OpenGraphException;
use App\ExternalApi\OpenGraph\OpenGraphFetcher;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class OpenGraphFetcherTest extends TestCase
{
    private function fetcher(MockResponse $response): OpenGraphFetcher
    {
        return new OpenGraphFetcher(
            new MockHttpClient($response),
            new ArrayAdapter(),
            new NullLogger(),
            3600,
        );
    }

    private function html(string $head): MockResponse
    {
        return new MockResponse(
            "<!DOCTYPE html><html><head>$head</head><body></body></html>",
            ['response_headers' => ['content-type' => 'text/html; charset=utf-8']],
        );
    }

    public function testExtractsOpenGraphTags(): void
    {
        $data = $this->fetcher($this->html(
            '<meta property="og:title" content="Mon titre">'
            .'<meta property="og:description" content="Ma description">'
            .'<meta property="og:image" content="https://exemple.test/img.png">'
            .'<meta property="og:site_name" content="Exemple">'
            .'<meta property="og:url" content="https://exemple.test/page">'
        ))->fetch('https://exemple.test/page');

        self::assertSame('Mon titre', $data->title);
        self::assertSame('Ma description', $data->description);
        self::assertSame('https://exemple.test/img.png', $data->image);
        self::assertSame('Exemple', $data->siteName);
        self::assertSame('https://exemple.test/page', $data->url);
    }

    public function testFallsBackToTwitterAndStandardTags(): void
    {
        $data = $this->fetcher($this->html(
            '<title>Titre HTML</title>'
            .'<meta name="description" content="Description standard">'
            .'<meta name="twitter:image" content="https://exemple.test/tw.png">'
        ))->fetch('https://exemple.test/page');

        self::assertSame('Titre HTML', $data->title);
        self::assertSame('Description standard', $data->description);
        self::assertSame('https://exemple.test/tw.png', $data->image);
        // og:url absent → repli sur l'URL demandée.
        self::assertSame('https://exemple.test/page', $data->url);
    }

    public function testRejectsNonHttpUrl(): void
    {
        $this->expectException(OpenGraphException::class);
        $this->fetcher($this->html(''))->fetch('ftp://exemple.test/page');
    }

    public function testThrowsOnHttpError(): void
    {
        $fetcher = $this->fetcher(new MockResponse('', ['http_code' => 404]));

        $this->expectException(OpenGraphException::class);
        $fetcher->fetch('https://exemple.test/introuvable');
    }

    public function testThrowsOnNonHtmlContent(): void
    {
        $fetcher = $this->fetcher(new MockResponse(
            '{"hello":"world"}',
            ['response_headers' => ['content-type' => 'application/json']],
        ));

        $this->expectException(OpenGraphException::class);
        $fetcher->fetch('https://exemple.test/data.json');
    }
}
