<?php declare(strict_types=1);

namespace App\Framework\Database\Persistence;

use Doctrine\ORM\EntityManagerInterface;

class DoctrinePersistence implements PersistenceInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private iterable $committers)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function save($object, $commit = true): void
    {
        $this->entityManager->persist($object);
        if ($commit) {
            $this->commit();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object, $commit = true): void
    {
        $this->entityManager->remove($object);
        if ($commit) {
            $this->commit();
        }
    }

    public function commit(): void
    {
        $this->entityManager->flush();
        foreach ($this->committers as $committer) {
            $committer->commit();
        }
    }

    public function refresh(mixed $object): void
    {
        $this->entityManager->refresh($object);
    }

    public function clear(): void
    {
        $this->entityManager->clear();
    }
}
