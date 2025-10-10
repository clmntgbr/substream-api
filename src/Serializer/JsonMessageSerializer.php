<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class JsonMessageSerializer implements SerializerInterface
{
    public function __construct(
        private readonly string $apiUrl,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        throw new \RuntimeException('Decode not implemented');
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        if (!$message instanceof AsyncMessageInterface) {
            throw new \RuntimeException('The message must implement AsyncMessageInterface.');
        }

        dump($message->jsonSerialize());

        $data = [
            'class' => $message::class,
            'payload' => $message->jsonSerialize(),
            'webhook_url_success' => $this->apiUrl.'/'.$message->getWebhookUrlSuccess(),
            'webhook_url_failure' => $this->apiUrl.'/'.$message->getWebhookUrlFailure(),
        ];

        return [
            'body' => json_encode($data),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];
    }
}
