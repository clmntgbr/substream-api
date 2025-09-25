<?php

declare(strict_types=1);

namespace App\Core\Application\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class StreamDTO* abstract Transfer Object for Stream.
 */
abstract class StreamDTO
{
    public function __construct(
        #[Groups(['default'])]
        public ?\Symfony\Component\Uid\Uuid $id,
    ) {
    }
}
