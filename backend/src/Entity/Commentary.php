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
use App\Repository\CommentaryRepository;
use App\State\AuthorProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentaryRepository::class)]
#[ApiResource(
    description: 'Commentaire posté par un utilisateur sur un article.',
    normalizationContext: ['groups' => ['comment:read']],
    denormalizationContext: ['groups' => ['comment:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_USER')", processor: AuthorProcessor::class),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getAuthor() == user"),
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['article' => 'exact', 'author' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['datePosted', 'id'], arguments: ['orderParameterName' => 'order'])]
class Commentary implements Authored
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:read', 'article:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['comment:read', 'comment:write', 'article:read'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['comment:read', 'comment:write', 'article:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['comment:read', 'article:read'])]
    private ?\DateTimeImmutable $datePosted = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['comment:read', 'article:read'])]
    private int $approved = 0;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['comment:read', 'article:read'])]
    private int $disapproved = 0;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['comment:read', 'comment:write'])]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read', 'article:read'])]
    private ?User $author = null;

    public function __construct()
    {
        $this->datePosted = new \DateTimeImmutable();
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

    public function getDatePosted(): ?\DateTimeImmutable
    {
        return $this->datePosted;
    }

    public function setDatePosted(\DateTimeImmutable $datePosted): static
    {
        $this->datePosted = $datePosted;

        return $this;
    }

    public function getApproved(): int
    {
        return $this->approved;
    }

    public function setApproved(int $approved): static
    {
        $this->approved = max(0, $approved);

        return $this;
    }

    public function getDisapproved(): int
    {
        return $this->disapproved;
    }

    public function setDisapproved(int $disapproved): static
    {
        $this->disapproved = max(0, $disapproved);

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

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
}
