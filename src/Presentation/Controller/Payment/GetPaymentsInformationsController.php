<?php

namespace App\Presentation\Controller\Payment;

use App\Domain\Payment\Repository\PaymentRepository;
use App\Domain\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetPaymentsInformationsController extends AbstractController
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
    ) {
    }

    public function __invoke(#[CurrentUser()] User $user): JsonResponse
    {
        $payments = $this->paymentRepository->getPaymentStatsByUser($user);

        return new JsonResponse([
            'amount' => $payments['amount'] / 100,
            'count' => $payments['count'],
        ]);
    }
}
