<?php

declare(strict_types=1);

namespace App\CoreDD\Application\Payment\Command;

use App\CoreDD\Infrastructure\Payment\Stripe\StripeCheckoutSessionGatewayInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStripeCheckoutSessionCommandHandler
{
    public function __construct(
        private StripeCheckoutSessionGatewayInterface $stripeCheckoutSessionGateway,
    ) {
    }

    public function __invoke(CreateStripeCheckoutSessionCommand $command): string
    {
        return $this->stripeCheckoutSessionGateway->create($command->getPlan(), $command->getUser());
    }
}
