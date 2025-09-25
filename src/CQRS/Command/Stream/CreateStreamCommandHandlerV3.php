<?php

declare(strict_types=1);

namespace App\CQRS\Command\Stream;

use App\CQRS\Service\JobLifecycleService;
use App\CQRS\Trait\JobTrackingTrait;
use App\Service\StreamService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class CreateStreamCommandHandlerV3
{
    use JobTrackingTrait;

    public function __construct(
        private StreamService $streamService,
        JobLifecycleService $jobLifecycleService
    ) {
        $this->setJobLifecycleService($jobLifecycleService);
    }

    public function __invoke(CreateStreamCommand $command): Uuid
    {
        return $this->executeWithJobTracking(
            function () use ($command) {
                return $this->streamService->createStream(
                    $command->fileName,
                    $command->originalFileName,
                    $command->mimeType,
                    $command->size,
                    $command->url,
                    $command->user,
                    $command->options
                );
            },
            [],
            function ($stream) {
                return ['streamId' => $stream->getId()->toRfc4122()];
            }
        )->getId();
    }
}
