<?php

namespace App\Entity;

use App\Repository\CookieConsentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CookieConsentRepository::class)]
class CookieConsent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 30)]
    private string $type; // accepted_all | rejected_all | personalized

    #[ORM\Column(type: 'boolean')]
    private bool $statistiques = false;

    #[ORM\Column(type: 'boolean')]
    private bool $marketing = false;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isStatistiques(): bool
    {
        return $this->statistiques;
    }

    public function setStatistiques(bool $statistiques): static
    {
        $this->statistiques = $statistiques;

        return $this;
    }

    public function isMarketing(): bool
    {
        return $this->marketing;
    }

    public function setMarketing(bool $marketing): static
    {
        $this->marketing = $marketing;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
