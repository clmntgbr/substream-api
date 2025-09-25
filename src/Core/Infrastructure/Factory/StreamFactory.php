<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Factory;

use App\Entity\Stream;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * Class StreamFactory.
 * Creates Stream entities for testing purposes.
 * Uses Zenstruck Foundry to generate persistent test data.
 */
final class StreamFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Stream::class;
    }

    protected function defaults(): array|callable
    {
        return [
            ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
