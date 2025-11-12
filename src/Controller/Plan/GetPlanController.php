<?php

declare(strict_types=1);

namespace App\Controller\Plan;

use App\Core\Domain\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
class GetPlanController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function __invoke(#[CurrentUser] User $user): JsonResponse
    {
        $normalizedResponse = $this->normalizer->normalize($user->getPlan(), null, ['groups' => ['plan:read']]);

        return new JsonResponse($normalizedResponse, JsonResponse::HTTP_OK);
    }
}
