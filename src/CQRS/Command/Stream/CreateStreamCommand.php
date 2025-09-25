<?php

declare(strict_types=1);

namespace App\CQRS\Command\Stream;

use App\CQRS\Command\TrackableCommandInterface;
use App\Entity\Options;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class CreateStreamCommand implements TrackableCommandInterface
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $fileName,
        
        #[Assert\NotBlank]
        public readonly string $originalFileName,
        
        #[Assert\NotBlank]
        public readonly string $mimeType,
        
        #[Assert\Positive]
        public readonly int $size,
        
        #[Assert\NotBlank]
        public readonly string $url,
        
        #[Assert\NotNull]
        public readonly User $user,
        
        #[Assert\NotNull]
        public readonly Options $options
    ) {
    }
}
