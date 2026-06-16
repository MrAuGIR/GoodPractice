<?php

namespace App\Controller;

use App\ExternalApi\OpenGraph\OpenGraphException;
use App\ExternalApi\OpenGraph\OpenGraphFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Récupère les métadonnées OpenGraph d'une URL pour pré-remplir un article
 * (réservé ROLE_EDITOR). Lecture seule : ne modifie aucune entité.
 * Corps : {"url": "https://…"}.
 */
#[Route('/api/enrich/opengraph', name: 'api_enrich_opengraph', methods: ['POST'])]
#[IsGranted('ROLE_EDITOR')]
class EnrichOpenGraphController extends AbstractController
{
    public function __construct(private OpenGraphFetcher $fetcher)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $url = \is_array($payload) ? ($payload['url'] ?? null) : null;

        if (!\is_string($url) || '' === trim($url)) {
            return $this->json(['error' => 'Champ "url" manquant ou invalide.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $data = $this->fetcher->fetch($url);
        } catch (OpenGraphException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json($data->toArray());
    }
}
