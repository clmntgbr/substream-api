<?php

declare(strict_types=1);

namespace App\Presentation\Webhook\Core;

use App\Domain\Core\Dto\ResizeVideoSuccess;
use Exception;
use SensitiveParameter;
use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class ResizeVideoSuccessRequestParser extends AbstractRequestParser
{
    public const WEBHOOK_NAME = 'resizevideosuccess';

    public function __construct(
        private DenormalizerInterface $denormalizer,
    ) {
    }

    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new IsJsonRequestMatcher(),
            new MethodRequestMatcher('POST'),
        ]);
    }

    /**
     * @throws JsonException
     */
    protected function doParse(Request $request, #[SensitiveParameter] string $secret): RemoteEvent
    {
        $authToken = $request->headers->get('X-Authentication-Token');
        if ($authToken !== $secret) {
            throw new RejectWebhookException(Response::HTTP_UNAUTHORIZED, 'Invalid authentication token.');
        }

        if (! $request->getPayload()->has('name') || ! $request->getPayload()->has('task_id')) {
            throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Request payload does not contain required fields.');
        }

        if (self::WEBHOOK_NAME !== $request->getPayload()->getString('name')) {
            throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Request payload name is not matching.');
        }

        try {
            $payload = $request->getPayload();
            $data = $this->denormalizer->denormalize($payload->all(), ResizeVideoSuccess::class);
        } catch (Exception $e) {
            throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Invalid payload');
        }

        return new RemoteEvent(
            $payload->getString('name'),
            $payload->getString('task_id'),
            ['payload' => $data],
        );
    }
}
