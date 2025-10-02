<?php

namespace App\RemoteEvent;

use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('extractsoundfailure')]
final class ExtractSoundFailureWebhookConsumer implements ConsumerInterface
{
    public function __construct()
    {
    }

    public function consume(RemoteEvent $event): void
    {
    }
}
