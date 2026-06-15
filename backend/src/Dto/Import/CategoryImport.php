<?php

namespace App\Dto\Import;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Catégorie telle que fournie dans le JSON d'import.
 */
class CategoryImport
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $name = null;

    #[Assert\Length(max: 255)]
    public ?string $defaultImage = null;
}
