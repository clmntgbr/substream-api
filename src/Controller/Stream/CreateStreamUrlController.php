<?php

namespace App\Controller\Stream;

use App\Shared\Domain\Response\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateStreamUrlController extends AbstractController
{
    public function __invoke(Request $request)
    {
        return Response::successResponse([]);
    }
}
