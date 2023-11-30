<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Book
{
    use Traits\TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message: 'Le nom du livre est obligatoire.')]
    #[Assert\Length(max: 255 , maxMessage: 'Le titre ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 5000 , maxMessage: 'La description ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank (message: 'La date de sortie est obligatoire.')]
    #[Assert\LessThanOrEqual(value: 'today', message: 'La date de sortie ne peut pas être dans le futur.')]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: 'Le nombre de pages est obligatoire.')]
    #[Assert\Positive (message: 'Le nombre de pages doit être positif.')]
    private ?int $nbPages = null;

    #[ORM\Column(length: 13, nullable: true)]
    #[Assert\Length(min: 10, max: 13, minMessage: 'L\'ISBN doit contenir au moins {{ limit }} caractères.', maxMessage: 'L\'ISBN ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $ISBN = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank (message: 'L\'auteur est obligatoire.')]
    private ?Author $author = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'books')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getNbPages(): ?int
    {
        return $this->nbPages;
    }

    public function setNbPages(int $nbPages): static
    {
        $this->nbPages = $nbPages;

        return $this;
    }

    public function getISBN(): ?string
    {
        return $this->ISBN;
    }

    public function setISBN(?string $ISBN): static
    {
        $this->ISBN = $ISBN;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
