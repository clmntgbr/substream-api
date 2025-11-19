<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Subscription;

use App\Domain\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
class GetSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function __invoke(#[CurrentUser] User $user): JsonResponse
    {
        $normalizedResponse = $this->normalizer->normalize($user->getActiveSubscription(), null, ['groups' => ['subscription:read']]);

        return new JsonResponse($normalizedResponse, JsonResponse::HTTP_OK);
    }
}
