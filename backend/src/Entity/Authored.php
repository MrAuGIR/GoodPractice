<?php

namespace App\Entity;

/**
 * Ressource possédant un auteur, assigné automatiquement à la création
 * via App\State\AuthorProcessor.
 */
interface Authored
{
    public function getAuthor(): ?User;

    public function setAuthor(?User $author): static;
}
