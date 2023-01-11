<?php declare(strict_types=1);

namespace App\Framework\EventListener\EntityHandling;

use App\Entity\Base\Entity;
use App\Entity\Base\IdEntity;
use App\Security\SecurityHelper;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrinePrePersistListener
{
    public function __construct(private SecurityHelper $security)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof IdEntity) {
            $this->addCreatedAt($entity);
            $this->addCreatedBy($entity);
        } else {
            throw new \UnexpectedValueException("Expected IdEntity, but got " . $entity::class);
        }
    }

    private function addCreatedAt(Entity $entity): void
    {
        $entity->setCreatedAt(new \DateTime('now'));
    }

    private function addCreatedBy(Entity $entity): void
    {
        $entity->setCreatedBy($this->security->getUser());
    }
}
