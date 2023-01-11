<?php declare(strict_types=1);

namespace App\Entity\Base;

use App\Entity\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * Absolute minimum bare entity class. Contains created_at and updated_at fields with relevant triggers,
 * but no primary key field (for flexibility).
 */
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class Entity
{
    private bool $renewUpdate = true;

    #[ORM\Column(type: "datetimetz", nullable: false)]
    protected ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: "datetimetz", nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    private ?Person $createdBy = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    private ?Person $updatedBy = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt ?? new \DateTime();
    }

    public function getCreatedBy(): ?Person
    {
        return $this->createdBy;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getUpdatedBy(): ?Person
    {
        return $this->updatedBy;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        if ($this->createdAt) {
            throw new \LogicException('"Created at" value can not be edited');
        }
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setCreatedBy(?Person $createdBy): void
    {
        if ($this->createdBy) {
            throw new \LogicException('"Created by" value can not be edited');
        }
        $this->createdBy = $createdBy;
    }

    public function setUpdatedBy(Person $updatedBy): void
    {
        $this->updatedBy = $updatedBy->getUpdatedBy();
    }
}
