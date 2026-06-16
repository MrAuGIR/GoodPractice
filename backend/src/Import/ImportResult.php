<?php

namespace App\Import;

/**
 * Résumé d'un import de bonnes pratiques.
 */
class ImportResult
{
    public int $categoriesCreated = 0;
    public int $tagsCreated = 0;
    public int $articlesCreated = 0;
    public int $articlesUpdated = 0;
    public int $articlesSkipped = 0;
    public bool $dryRun = false;

    /**
     * @return array<string, int|bool>
     */
    public function toArray(): array
    {
        return [
            'dryRun' => $this->dryRun,
            'categoriesCreated' => $this->categoriesCreated,
            'tagsCreated' => $this->tagsCreated,
            'articlesCreated' => $this->articlesCreated,
            'articlesUpdated' => $this->articlesUpdated,
            'articlesSkipped' => $this->articlesSkipped,
        ];
    }
}
