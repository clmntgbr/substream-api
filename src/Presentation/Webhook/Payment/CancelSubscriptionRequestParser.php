<?php

namespace App\Presentation\Webhook\Payment;

use Stripe\Webhook;
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

final class CancelSubscriptionRequestParser extends AbstractRequestParser
{
    public const WEBHOOK_NAME = 'subscriptiondeleted';
    public const EVENT_TYPE_SUBSCRIPTION_DELETED = 'customer.subscription.deleted';

    public function __construct()
    {
    }

    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new MethodRequestMatcher('POST'),
            new IsJsonRequestMatcher(),
        ]);
    }

    /**
     * @throws JsonException
     */
    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): RemoteEvent
    {
        $event = Webhook::constructEvent(
            $request->getContent(),
            $request->headers->all()['stripe-signature'][0] ?? '',
            $secret
        );

        if (self::EVENT_TYPE_SUBSCRIPTION_DELETED !== $event->type) {
            throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Invalid event type.');
        }

        return new RemoteEvent(
            $event->id,
            $event->type,
            ['payload' => $event],
        );
    }
}
