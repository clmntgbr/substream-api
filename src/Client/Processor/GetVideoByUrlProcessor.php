<?php

namespace App\Client\Processor;

use App\Client\AsyncProcessorClientInterface;
use App\Dto\Processor\GetVideoByUrl;

final class GetVideoByUrlProcessor implements GetVideoByUrlProcessorInterface
{
    const GET_VIDEO_BY_URL_URL = '/api/download/video/url';

    public function __construct(
        private AsyncProcessorClientInterface $processorClient,
    ) {
    }

    public function __invoke(GetVideoByUrl $dto): void
    {
        $this->processorClient->request('POST', self::GET_VIDEO_BY_URL_URL, [
            'json' => $dto->jsonSerialize(),
        ]);
    }
}