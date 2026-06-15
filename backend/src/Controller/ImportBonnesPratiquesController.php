<?php

namespace App\Controller;

use App\Import\BonnesPratiquesImporter;
use App\Import\ImportException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Upload admin d'un JSON de bonnes pratiques (réservé ROLE_ADMIN).
 * Le corps de la requête est le JSON d'import ; `?dry-run=1` simule sans écrire.
 */
#[Route('/api/import/bonnes-pratiques', name: 'api_import_bonnes_pratiques', methods: ['POST'])]
#[IsGranted('ROLE_ADMIN')]
class ImportBonnesPratiquesController extends AbstractController
{
    public function __construct(private BonnesPratiquesImporter $importer)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $json = $request->getContent();
        if ('' === trim($json)) {
            return $this->json(['errors' => ['Corps de requête vide : envoyez le contenu JSON.']], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->importer->importFromJson($json, $request->query->getBoolean('dry-run'));
        } catch (ImportException $e) {
            return $this->json(['errors' => $e->getErrors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json($result->toArray());
    }
}
