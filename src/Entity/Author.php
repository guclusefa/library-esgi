<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Author
{
    use Traits\TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message: 'Le prénom est obligatoire.')]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message: 'Le nom est obligatoire.')]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 5000 , maxMessage: 'La biographie ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $biography = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\LessThanOrEqual(value: 'today', message: 'La date de naissance ne peut pas être dans le futur.')]
    #[Assert\LessThanOrEqual(value: '-10 years', message: 'L\'auteur doit avoir au moins 10 ans.')]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Assert\Length(max: 5 , maxMessage: 'Le pays d\'origine ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $origin = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Book::class, orphanRemoval: true)]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): static
    {
        $this->biography = $biography;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): static
    {
        $this->origin = $origin;

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

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getAge(): ?int
    {
        if ($this->birthDate === null) {
            return null;
        }

        $now = new \DateTime();
        $interval = $now->diff($this->birthDate);

        return $interval->y;
    }
}
