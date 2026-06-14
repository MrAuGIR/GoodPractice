<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ArticleRepository;
use App\State\AuthorProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    description: 'Article de bonne pratique, rattaché à une catégorie et à un auteur.',
    normalizationContext: ['groups' => ['article:read']],
    denormalizationContext: ['groups' => ['article:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_EDITOR')", processor: AuthorProcessor::class),
        new Put(security: "is_granted('ROLE_ADMIN') or object.getAuthor() == user"),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getAuthor() == user"),
    ],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'category' => 'exact',
    'category.name' => 'partial',
    'author' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['dateCreate', 'title', 'id'], arguments: ['orderParameterName' => 'order'])]
class Article implements Authored
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['article:read', 'comment:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['article:read', 'article:write', 'comment:read'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['article:read', 'article:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $urlImg = null;

    #[ORM\Column]
    #[Groups(['article:read'])]
    private ?\DateTimeImmutable $dateCreate = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['article:read'])]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['article:read', 'article:write'])]
    private ?Category $category = null;

    /** @var Collection<int, Commentary> */
    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Commentary::class, orphanRemoval: true)]
    #[Groups(['article:read'])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->dateCreate = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUrlImg(): ?string
    {
        return $this->urlImg;
    }

    public function setUrlImg(?string $urlImg): static
    {
        $this->urlImg = $urlImg;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeImmutable
    {
        return $this->dateCreate;
    }

    public function setDateCreate(\DateTimeImmutable $dateCreate): static
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Commentary>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Nombre de commentaires (calculé, non stocké).
     */
    #[Groups(['article:read'])]
    public function getNbComments(): int
    {
        return $this->comments->count();
    }
}
