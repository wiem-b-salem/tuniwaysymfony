<?php

namespace App\Entity;

use App\Repository\TourPersonnaliseRepository;
use App\Entity\Review;
use App\Entity\Reservation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TourPersonnaliseRepository::class)]
class TourPersonnalise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tour:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    #[Groups(['tour:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['tour:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['tour:read'])]
    private ?int $duration = null;

    #[ORM\Column]
    #[Groups(['tour:read'])]
    private ?float $price = null;

    #[ORM\Column]
    #[Groups(['tour:read'])]
    private ?int $maxPersons = null;

    #[ORM\Column]
    #[Groups(['tour:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'tourPersonnalises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $guide = null;

    #[ORM\ManyToOne(inversedBy: 'tourPersonnalises')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $client = null;

    /**
     * @var Collection<int, Place>
     */
    #[ORM\ManyToMany(targetEntity: Place::class)]
    private Collection $places;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'tour')]
    private Collection $reviews;

    public function __construct()
    {
        $this->places = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    // ... (keep existing methods)

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'tour')]
    private Collection $reservations;

    // ... (keep review collection getter)

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setTour($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getTour() === $this) {
                $reservation->setTour(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setTour($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getTour() === $this) {
                $review->setTour(null);
            }
        }

        return $this;
    }
    // ... (removed closing brace)


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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getMaxPersons(): ?int
    {
        return $this->maxPersons;
    }

    public function setMaxPersons(int $maxPersons): static
    {
        $this->maxPersons = $maxPersons;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getGuide(): ?User
    {
        return $this->guide;
    }

    public function setGuide(?User $guide): static
    {
        $this->guide = $guide;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): static
    {
        if (!$this->places->contains($place)) {
            $this->places->add($place);
        }

        return $this;
    }

    public function removePlace(Place $place): static
    {
        $this->places->removeElement($place);

        return $this;
    }
}
