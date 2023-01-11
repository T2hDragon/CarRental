<?php declare(strict_types=1);

namespace App\Entity\Base;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class IdEntity extends Entity
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isSameAs(mixed $comparable): bool
    {
        if (!is_object($comparable) || !($comparable::class === static::class)) {
            return false;
        }
        return $this->getId() === $comparable->getId() && $this->getId() !== null;
    }
}
