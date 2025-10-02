<?php

namespace App\Webhook;

use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class ExtractSoundFailureRequestParser extends AbstractRequestParser
{
    public const WEBHOOK_NAME = 'extractsoundfailure';
    
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
    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        $authToken = $request->headers->get('X-Authentication-Token');
        if ($authToken !== $secret) {
            throw new RejectWebhookException(Response::HTTP_UNAUTHORIZED, 'Invalid authentication token.');
        }

        if (!$request->getPayload()->has('name') || !$request->getPayload()->has('id')) {
            throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Request payload does not contain required fields.');
        }

        if (self::WEBHOOK_NAME !== $request->getPayload()->getString('name')) {
            throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Request payload name is not matching.');
        }

        // Parse the request payload and return a RemoteEvent object.
        $payload = $request->getPayload();

        return new RemoteEvent(
            $payload->getString('name'),
            $payload->getString('id'),
            $payload->all(),
        );
    }
}
