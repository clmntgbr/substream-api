<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class JsonMessageSerializer implements SerializerInterface
{
    public function __construct() 
    {
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

        $data = [
            'task_id' => Uuid::v4(),
            'class' => $message::class,
            'payload' => $message->jsonSerialize(),
            'webhook_url_success' => $message->getWebhookUrlSuccess(),
            'webhook_url_failure' => $message->getWebhookUrlFailure(),
        ];

        return [
            'body' => json_encode($data),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];
    }
}
