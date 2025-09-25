<?php

declare(strict_types=1);

namespace App\Core\Presentation\Controller;

use App\Core\Application\DTO\StreamRequestDTO;
use App\Shared\Domain\Response\Response;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/streams/findbyid', name: 'api_v1_stream_findbyid', methods: ['GET'])]
#[OA\Get(
    path: '/api/v1/streams/findbyid',
    summary: 'Retrieves Stream by id.',
    tags: ['Streams'],
    parameters: [
    new OA\Parameter(
        name: 'id',
        description: 'Parameter id.',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string', default: 1)
    ),
    ],
    responses: [
    new OA\Response(
        response: 200,
        description: 'Find  Stream by Id successfully.',
        content: new OA\JsonContent(
            properties: [
            new OA\Property(property: 'success', type: 'boolean', example: true),
            new OA\Property(
                property: 'data',
                properties: [
                new OA\Property(property: 'items', type: 'array', items: new OA\Items(new Model(type: StreamRequestDTO::class, groups: ['default']))),
                ],
                type: 'object'
            ),
            new OA\Property(property: 'message', type: 'string', example: 'Streams retrieved successfully.'),
            ]
        )
    ),
    new OA\Response(
        response: 400,
        description: 'An error occurred while retrieving Streams.',
        content: new OA\JsonContent(
            properties: [
            new OA\Property(property: 'success', type: 'boolean', example: false),
            new OA\Property(property: 'message', type: 'string', example: 'Error message'),
            ]
        )
    ),
    ]
)]
class FindByIdStreamController extends \App\Shared\Presentation\Controller\BaseController
{
    public function __construct(
        private \App\Shared\Application\Bus\QueryBus $query_bus,
        private \App\Core\Application\Mapper\Stream\StreamMapperInterface $mapper
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $parameter = $request->query->get('id', null);

            if (!$parameter) {
                return Response::errorResponse('id is required in query', 400);
            }

            $query = new \App\Core\Application\Query\FindByIdStreamQuery(
                id: \App\Core\Domain\ValueObject\StreamId::create($parameter),
            );

            $response = $this->query_bus->dispatch($query);

            return Response::successResponse(
                [
                'items' => $response,
                ]
            );
        } catch (\Exception $e) {
            return Response::errorResponse($e->getMessage(), 400);
        }
    }
}
