<?php

namespace App\Controller\Stream;

use App\Core\Application\Command\CreateStreamUrlCommand;
use App\Dto\CreateStreamUrlParam;
use App\Entity\User;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Domain\Response\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class CreateStreamUrlController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(#[MapRequestPayload] CreateStreamUrlParam $param, #[CurrentUser] User $user)
    {
        try {
            $createStreamModel = $this->commandBus->dispatch(new CreateStreamUrlCommand($param->url, $user));

            return Response::successResponse([
                'streamId' => $createStreamModel->streamId,
            ]);
        } catch (\Throwable $exception) {
            return Response::errorResponse($exception->getMessage());
        }
    }
}
