<?php

namespace App\Import;

/**
 * Erreur d'import (JSON invalide, validation, contexte manquant).
 * Porte une liste de messages lisibles destinés à l'utilisateur.
 */
class ImportException extends \RuntimeException
{
    /**
     * @param list<string> $errors
     */
    public function __construct(private array $errors)
    {
        parent::__construct(implode(' | ', $errors));
    }

    /**
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
