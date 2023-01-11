<?php declare(strict_types=1);

namespace App\Framework\EventListener\EntityHandling;

use App\Entity\Base\Entity;
use App\Security\SecurityHelper;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class DoctrinePreUpdateListener
 */
class DoctrinePreUpdateListener
{
    public function __construct(private SecurityHelper $security)
    {
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Entity) {
            $this->addUpdatedAt($entity);
            $this->addUpdatedBy($entity);
        }
    }

    private function addUpdatedAt(Entity $entity): void
    {
        $entity->setUpdatedAt(new \DateTime('now'));
    }

    private function addUpdatedBy(Entity $entity): void
    {
            $entity->setUpdatedBy($this->security->getUser());
    }
}
