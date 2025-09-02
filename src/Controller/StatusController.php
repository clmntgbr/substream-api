<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class StatusController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[Route(path: '/api/status', name: 'app_status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        $this->logger->error('Status request');

        return new JsonResponse(['status' => 'ok']);
    }
}
