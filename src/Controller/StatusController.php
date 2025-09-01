<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class StatusController extends AbstractController
{
    #[Route(path: '/api/status', name: 'app_status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
