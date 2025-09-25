<?php

declare(strict_types=1);

namespace App\Core\Presentation\Controller;

use App\Core\Application\DTO\StreamRequestDTO;
use App\Shared\Domain\DTO\ApiResponseDTO;
use App\Shared\Domain\DTO\ErrorResponseDTO;
use App\Shared\Domain\Response\Response;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/streams/create/', name: 'api_command_v1_stream_create', methods: ['POST'])]
#[OA\Post(
    path: '/api/v1/streams/create/',
    summary: 'Creates a new Stream item.',
    requestBody: new OA\RequestBody(
        description: 'Data required to create a Stream.',
        required: true,
        content: new Model(type: StreamRequestDTO::class, groups: ['default'])
    ),
    tags: ['Streams'],
    responses: [
    new OA\Response(
        response: 201,
        description: 'Stream created successfully.',
        content: new Model(type: ApiResponseDTO::class, groups: ['default'])
    ),
    new OA\Response(
        response: 400,
        description: 'Invalid input.',
        content: new Model(type: ErrorResponseDTO::class, groups: ['error'])
    ),
    ]
)]
class CreateStreamController extends \App\Shared\Presentation\Controller\BaseController
{
    public function __construct(
        private \App\Shared\Application\Bus\CommandBus $command_bus,
        private \App\Core\Application\Mapper\Stream\StreamMapperInterface $mapper
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $command_model = $this->mapper->fromArray($data);
            $command = new \App\Core\Application\Command\CreateStreamCommand(
                fileName: $command_model->fileName,
                originalFileName: $command_model->originalFileName,
                url: $command_model->url,
            );
            $model = $this->command_bus->dispatch($command);
            $responseDTO = $this->mapper->toArray($model);

            return Response::successResponse($responseDTO, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return Response::errorResponse($e->getMessage());
        }
    }
}
