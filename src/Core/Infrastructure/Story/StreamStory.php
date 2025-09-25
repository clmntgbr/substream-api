<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Story;

use Zenstruck\Foundry\Story;

/**
 * Class StreamStory.
 * Story to create 15 instances of Stream using the factory.
 */
final class StreamStory extends Story
{
    public function build(): void
    {
        \App\Core\Infrastructure\Factory\StreamFactory::createMany(15);
    }
}
