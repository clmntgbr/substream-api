<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Serializer;

use App\Shared\Application\Message\AsynchronousMessageInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

use function Safe\json_encode;

class JsonMessageSerializer implements SerializerInterface
{
    public function __construct(
        private readonly string $apiUrl,
    ) {
    }

    /**
     * @param array<string, mixed> $encodedEnvelope
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        throw new \RuntimeException('Decode not implemented');
    }

    /**
     * @return array<string, mixed>
     */
    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        if (!$message instanceof AsynchronousMessageInterface) {
            throw new \RuntimeException('The message must implement AsynchronousMessageInterface.');
        }

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
