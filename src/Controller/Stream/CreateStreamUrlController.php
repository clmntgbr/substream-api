<?php

namespace App\Controller\Stream;

use App\Core\Application\Command\Sync\CreateStreamUrlCommand;
use App\Dto\CreateStreamUrl;
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

    public function __invoke(#[MapRequestPayload] CreateStreamUrl $param, #[CurrentUser] User $user)
    {
        $createStreamModel = $this->commandBus->dispatch(
            new CreateStreamUrlCommand(
                url: $param->getUrl(),
                user: $user,
            ),
        );

        return Response::successResponse([
            'streamId' => $createStreamModel->streamId,
        ]);
    }
}
