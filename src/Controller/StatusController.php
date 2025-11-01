<?php

declare(strict_types=1);

namespace App\Controller;

use App\Shared\Domain\Response\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class StatusController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route(path: '/api/status', name: 'app_status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        return Response::successResponse(['status' => 'ok']);
    }
}
