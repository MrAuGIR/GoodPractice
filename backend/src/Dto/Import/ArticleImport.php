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
    #[Assert\Url]
    public ?string $url = null;

    #[Assert\Length(max: 255)]
    #[Assert\Url]
    public ?string $urlImg = null;
}
