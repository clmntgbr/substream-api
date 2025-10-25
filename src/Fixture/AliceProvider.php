<?php

declare(strict_types=1);

namespace App\Fixture;

use Faker\Generator;
use Faker\Provider\Base;

final class AliceProvider extends Base
{
    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
    }

    public function envar(string $name): string
    {
        $value = $_SERVER[$name] ?? $_ENV[$name] ?? getenv($name);
        
        if ($value === false) {
            throw new \RuntimeException(sprintf('Environment variable "%s" is not set.', $name));
        }

        return $value;
    }
}
