<?php declare(strict_types=1);

namespace App\Framework\Database\Persistence;

/**
 * Interface PersistenceInterface
 */
interface PersistenceInterface
{
    public function save(mixed $object, bool $commit = true): void;

    public function delete(mixed $object, bool $commit = true): void;

    public function commit(): void;

    public function refresh(mixed $object): void;

    public function clear(): void;
}
