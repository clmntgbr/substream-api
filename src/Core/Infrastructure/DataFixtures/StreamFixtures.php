<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\DataFixtures;

use App\Core\Infrastructure\Story\StreamStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class StreamFixtures.
 * Seeds the database with initial data using the story.
 */
class StreamFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        StreamStory::load();
    }
}
