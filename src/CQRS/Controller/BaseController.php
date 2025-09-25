<?php

declare(strict_types=1);

namespace App\CQRS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'command.bus')]
        protected MessageBusInterface $commandBus,
        #[Autowire(service: 'query.bus')]
        protected MessageBusInterface $queryBus,
        protected ValidatorInterface $validator
    ) {
    }
}
