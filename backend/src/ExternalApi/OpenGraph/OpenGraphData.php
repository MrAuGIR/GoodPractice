<?php

namespace App\ExternalApi\OpenGraph;

/**
 * Métadonnées OpenGraph extraites d'une page distante.
 * Tous les champs sont optionnels (la page peut n'en exposer aucun).
 */
final readonly class OpenGraphData
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $siteName = null,
        public ?string $url = null,
    ) {
    }

    /**
     * @return array{title: ?string, description: ?string, image: ?string, siteName: ?string, url: ?string}
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'siteName' => $this->siteName,
            'url' => $this->url,
        ];
    }
}
