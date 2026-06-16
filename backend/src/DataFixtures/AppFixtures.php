<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Commentary;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // --- Utilisateurs ---
        $admin = $this->makeUser('admin@goodpractice.test', 'Admin', ['ROLE_ADMIN'], 'password');
        $editor = $this->makeUser('editor@goodpractice.test', 'Éditeur', ['ROLE_EDITOR'], 'password');
        $user = $this->makeUser('user@goodpractice.test', 'Lecteur', [], 'password');

        $manager->persist($admin);
        $manager->persist($editor);
        $manager->persist($user);

        // --- Catégories ---
        $categoriesData = [
            'Green IT' => 'green-it.png',
            'Performance' => 'performance.png',
            'Sécurité' => 'security.png',
            'Architecture' => 'architecture.png',
        ];
        $categories = [];
        foreach ($categoriesData as $name => $img) {
            $category = (new Category())
                ->setName($name)
                ->setDefaultImage($img);
            $manager->persist($category);
            $categories[] = $category;
        }

        // --- Tags transverses ---
        $tagNames = ['Frontend', 'Backend', 'Performance', 'Accessibilité', 'Sécurité', 'Images', 'Cache', 'Débutant', 'Green IT', 'Tests'];
        $tags = [];
        foreach ($tagNames as $name) {
            $tag = (new Tag())->setName($name);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        // --- Articles + commentaires ---
        $authors = [$admin, $editor];
        $tagCount = count($tags);
        for ($i = 1; $i <= 8; ++$i) {
            $article = (new Article())
                ->setTitle("Bonne pratique n°$i")
                ->setDescription("Description détaillée de la bonne pratique n°$i pour écrire du code propre et maintenable.")
                ->setUrl(0 === $i % 2 ? "https://example.com/ressource-$i" : null)
                ->setUrlImg('illustration/default.png')
                ->setCategory($categories[$i % count($categories)])
                ->setAuthor($authors[$i % count($authors)])
                ->setFeatured($i <= 2);

            // Quelques tags transverses par article (déterministes).
            foreach ([$i, $i + 3, $i + 6] as $offset) {
                $article->addTag($tags[$offset % $tagCount]);
            }

            $manager->persist($article);

            // Quelques commentaires par article
            for ($c = 1; $c <= ($i % 3); ++$c) {
                $comment = (new Commentary())
                    ->setTitle("Commentaire $c sur l'article $i")
                    ->setDescription("Très utile, merci pour le partage ! ($c)")
                    ->setApproved($c)
                    ->setDisapproved(0)
                    ->setArticle($article)
                    ->setAuthor($user);
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    /**
     * @param list<string> $roles
     */
    private function makeUser(string $email, string $name, array $roles, string $plainPassword): User
    {
        $user = (new User())
            ->setEmail($email)
            ->setName($name)
            ->setRoles($roles);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        return $user;
    }
}
