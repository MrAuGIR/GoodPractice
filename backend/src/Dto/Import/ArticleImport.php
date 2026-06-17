<?php

namespace App\Dto\Import;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article (bonne pratique) tel que fourni dans le JSON d'import.
 * `category` référence une catégorie par son nom.
 */
class ArticleImport
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $title = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $category = null;

    #[Assert\NotBlank]
    public ?string $description = null;

    #[Assert\Length(max: 255)]
    #[Assert\Url(requireTld: true)]
    public ?string $url = null;

    #[Assert\Length(max: 255)]
    #[Assert\Url(requireTld: true)]
    public ?string $urlImg = null;

    /**
     * Tags transverses référencés par leur nom (créés à la volée si absents).
     *
     * @var string[]
     */
    #[Assert\All([
        new Assert\NotBlank(),
        new Assert\Length(max: 60),
    ])]
    public array $tags = [];

    public bool $featured = false;
}
