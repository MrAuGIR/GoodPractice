<?php

namespace App\ExternalApi\OpenGraph;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Récupère une page distante et en extrait les métadonnées OpenGraph
 * (avec fallbacks Twitter Card et balises HTML standard). Le résultat est
 * mis en cache par URL pour éviter de re-télécharger la même page.
 */
final class OpenGraphFetcher
{
    /** Taille max du HTML lu (octets) pour éviter de charger des pages énormes. */
    private const MAX_BYTES = 2_000_000;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $openGraphCache,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(int:OPENGRAPH_CACHE_TTL)%')]
        private readonly int $ttl,
    ) {
    }

    public function fetch(string $url): OpenGraphData
    {
        $url = trim($url);
        if (!filter_var($url, FILTER_VALIDATE_URL) || !\in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
            throw new OpenGraphException('URL invalide : seuls les liens http(s) sont acceptés.');
        }

        $cached = $this->openGraphCache->get('og_'.sha1($url), function (ItemInterface $item) use ($url): array {
            $item->expiresAfter($this->ttl);

            return $this->parse($url, $this->download($url));
        });

        return new OpenGraphData(...$cached);
    }

    private function download(string $url): string
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 5,
                'max_redirects' => 3,
                'headers' => ['User-Agent' => 'goodPractice-OpenGraphBot/1.0'],
            ]);

            if ($response->getStatusCode() >= 400) {
                throw new OpenGraphException(sprintf('La page a répondu avec un statut %d.', $response->getStatusCode()));
            }

            $contentType = $response->getHeaders(false)['content-type'][0] ?? '';
            if ('' !== $contentType && !str_contains($contentType, 'text/html')) {
                throw new OpenGraphException('Le contenu distant n\'est pas une page HTML.');
            }

            $html = '';
            foreach ($this->httpClient->stream($response) as $chunk) {
                $html .= $chunk->getContent();
                if (\strlen($html) > self::MAX_BYTES) {
                    break;
                }
            }

            return $html;
        } catch (OpenGraphException $e) {
            throw $e;
        } catch (HttpExceptionInterface $e) {
            $this->logger->warning('OpenGraph fetch failed', ['url' => $url, 'error' => $e->getMessage()]);

            throw new OpenGraphException('Page distante inaccessible (délai dépassé ou erreur réseau).');
        }
    }

    /**
     * @return array{title: ?string, description: ?string, image: ?string, siteName: ?string, url: ?string}
     */
    private function parse(string $url, string $html): array
    {
        if ('' === trim($html)) {
            throw new OpenGraphException('Page distante vide.');
        }

        $dom = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new \DOMXPath($dom);

        $meta = static function (string $expr) use ($xpath): ?string {
            $value = $xpath->evaluate('string('.$expr.')');

            return \is_string($value) && '' !== trim($value) ? trim($value) : null;
        };

        $title = $meta('//meta[@property="og:title"]/@content')
            ?? $meta('//meta[@name="twitter:title"]/@content')
            ?? $meta('//title');

        $description = $meta('//meta[@property="og:description"]/@content')
            ?? $meta('//meta[@name="twitter:description"]/@content')
            ?? $meta('//meta[@name="description"]/@content');

        $image = $meta('//meta[@property="og:image"]/@content')
            ?? $meta('//meta[@name="twitter:image"]/@content');

        return [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'siteName' => $meta('//meta[@property="og:site_name"]/@content'),
            'url' => $meta('//meta[@property="og:url"]/@content') ?? $url,
        ];
    }
}
