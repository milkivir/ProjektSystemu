<?php

namespace App\Entity;

use App\Repository\ReservationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationsRepository::class)]
class Reservations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToMany(mappedBy: 'reservations', targetEntity: user::class)]
    private Collection $user_id;

    #[ORM\Column]
    private ?int $booker = null;

    #[ORM\Column]
    private ?bool $is_confirmed = null;

    #[ORM\Column(nullable: true)]
    private ?int $photographer_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $booker_email = null;

    public function __construct()
    {
        $this->user_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getUserId(): Collection
    {
        return $this->user_id;
    }

    public function addUserId(user $userId): self
    {
        if (!$this->user_id->contains($userId)) {
            $this->user_id->add($userId);
            $userId->setReservations($this);
        }

        return $this;
    }

    public function removeUserId(user $userId): self
    {
        if ($this->user_id->removeElement($userId)) {
            // set the owning side to null (unless already changed)
            if ($userId->getReservations() === $this) {
                $userId->setReservations(null);
            }
        }

        return $this;
    }

    public function getBooker(): ?int
    {
        return $this->booker;
    }

    public function setBooker(int $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function isIsConfirmed(): ?bool
    {
        return $this->is_confirmed;
    }

    public function setIsConfirmed(bool $is_confirmed): self
    {
        $this->is_confirmed = $is_confirmed;

        return $this;
    }

    public function getPhotographerId(): ?int
    {
        return $this->photographer_id;
    }

    public function setPhotographerId(?int $photographer_id): self
    {
        $this->photographer_id = $photographer_id;

        return $this;
    }

    public function getBookerEmail(): ?string
    {
        return $this->booker_email;
    }

    public function setBookerEmail(?string $booker_email): self
    {
        $this->booker_email = $booker_email;

        return $this;
    }
}
