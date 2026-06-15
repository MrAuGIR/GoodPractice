<?php

namespace App\Import;

use App\Dto\Import\ArticleImport;
use App\Dto\Import\CategoryImport;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Importe un corpus de bonnes pratiques (catégories + articles) depuis du JSON.
 *
 * - upsert des catégories par `name`, des articles par `title` (réimport idempotent) ;
 * - les catégories référencées par un article sont créées à la volée si absentes ;
 * - l'auteur des nouveaux articles = premier compte ROLE_ADMIN.
 *
 * Réutilisable par la commande console et l'upload admin.
 */
class BonnesPratiquesImporter
{
    public function __construct(
        private DenormalizerInterface $serializer,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws ImportException si le JSON est invalide, la validation échoue ou aucun admin n'existe
     */
    public function importFromJson(string $json, bool $dryRun = false): ImportResult
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException(['JSON invalide : '.$e->getMessage()]);
        }

        if (!\is_array($data)) {
            throw new ImportException(['Le JSON racine doit être un objet { "categories": [...], "articles": [...] }.']);
        }

        /** @var CategoryImport[] $categories */
        $categories = $this->denormalizeList($data['categories'] ?? [], CategoryImport::class, 'categories');
        /** @var ArticleImport[] $articles */
        $articles = $this->denormalizeList($data['articles'] ?? [], ArticleImport::class, 'articles');

        if (!$articles) {
            throw new ImportException(['Aucun article à importer (clé "articles" vide).']);
        }

        $errors = $this->validate($categories, $articles);
        if ($errors) {
            throw new ImportException($errors);
        }

        $author = $this->userRepository->findOneAdmin();
        if (!$author instanceof User) {
            throw new ImportException(['Aucun utilisateur ROLE_ADMIN pour rattacher les articles importés.']);
        }

        $result = new ImportResult();
        $result->dryRun = $dryRun;

        /** @var array<string, Category> $catCache */
        $catCache = [];
        /** @var array<string, true> $seenTitles */
        $seenTitles = [];

        // Catégories explicites
        foreach ($categories as $dto) {
            $this->getOrCreateCategory($dto->name, $dto->defaultImage, $catCache, $result, $dryRun);
        }

        // Articles (upsert par titre)
        foreach ($articles as $dto) {
            $titleKey = mb_strtolower(trim((string) $dto->title));
            if (isset($seenTitles[$titleKey])) {
                ++$result->articlesSkipped; // doublon dans le même JSON
                continue;
            }
            $seenTitles[$titleKey] = true;

            $category = $this->getOrCreateCategory($dto->category, null, $catCache, $result, $dryRun);
            $existing = $this->articleRepository->findOneBy(['title' => $dto->title]);

            if ($existing instanceof Article) {
                if (!$dryRun) {
                    $existing
                        ->setDescription((string) $dto->description)
                        ->setUrl($dto->url)
                        ->setUrlImg($dto->urlImg)
                        ->setCategory($category);
                }
                ++$result->articlesUpdated;
                continue;
            }

            if (!$dryRun) {
                $article = (new Article())
                    ->setTitle((string) $dto->title)
                    ->setDescription((string) $dto->description)
                    ->setUrl($dto->url)
                    ->setUrlImg($dto->urlImg)
                    ->setCategory($category)
                    ->setAuthor($author);
                $this->em->persist($article);
            }
            ++$result->articlesCreated;
        }

        if (!$dryRun) {
            $this->em->flush();
        }

        return $result;
    }

    /**
     * @param array<string, Category> $catCache
     */
    private function getOrCreateCategory(
        ?string $name,
        ?string $defaultImage,
        array &$catCache,
        ImportResult $result,
        bool $dryRun,
    ): Category {
        $key = mb_strtolower(trim((string) $name));
        if (isset($catCache[$key])) {
            return $catCache[$key];
        }

        $category = $this->categoryRepository->findOneBy(['name' => $name]);
        if (!$category instanceof Category) {
            $category = (new Category())->setName((string) $name);
            if ($defaultImage) {
                $category->setDefaultImage($defaultImage);
            }
            if (!$dryRun) {
                $this->em->persist($category);
            }
            ++$result->categoriesCreated;
        }

        return $catCache[$key] = $category;
    }

    /**
     * @param class-string $class
     *
     * @return object[]
     */
    private function denormalizeList(mixed $items, string $class, string $field): array
    {
        if (!\is_array($items)) {
            throw new ImportException([sprintf('La clé "%s" doit être un tableau.', $field)]);
        }

        try {
            return $this->serializer->denormalize($items, $class.'[]');
        } catch (SerializerException $e) {
            throw new ImportException([sprintf('Structure invalide dans "%s" : %s', $field, $e->getMessage())]);
        }
    }

    /**
     * @param CategoryImport[] $categories
     * @param ArticleImport[]  $articles
     *
     * @return list<string>
     */
    private function validate(array $categories, array $articles): array
    {
        $messages = [];
        foreach ($categories as $i => $dto) {
            foreach ($this->validator->validate($dto) as $violation) {
                $messages[] = sprintf('categories[%d].%s : %s', $i, $violation->getPropertyPath(), $violation->getMessage());
            }
        }
        foreach ($articles as $i => $dto) {
            foreach ($this->validator->validate($dto) as $violation) {
                $messages[] = sprintf('articles[%d].%s : %s', $i, $violation->getPropertyPath(), $violation->getMessage());
            }
        }

        return $messages;
    }
}
