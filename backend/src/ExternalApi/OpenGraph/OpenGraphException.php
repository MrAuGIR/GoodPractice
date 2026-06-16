<?php

namespace App\ExternalApi\OpenGraph;

/**
 * Erreur d'enrichissement OpenGraph (URL invalide, page inaccessible,
 * contenu non HTML…). Le message est court et présentable à l'utilisateur.
 */
class OpenGraphException extends \RuntimeException
{
}
