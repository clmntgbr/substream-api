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

#[Route('/api/v1/streams/list', name: 'api_v1_stream_find', methods: ['GET'])]
#[OA\Get(
    path: '/api/v1/streams/list',
    summary: 'Retrieves all Stream items with pagination.',
    tags: ['Streams'],
    parameters: [
    new OA\Parameter(
        name: 'page',
        description: 'Page number.',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 1)
    ),
    new OA\Parameter(
        name: 'limit',
        description: 'Number of items per page.',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 10)
    ),
    ],
    responses: [
    new OA\Response(
        response: 200,
        description: 'List of Stream items retrieved successfully.',
        content: new OA\JsonContent(
            properties: [
            new OA\Property(property: 'success', type: 'boolean', example: true),
            new OA\Property(
                property: 'data',
                properties: [
                new OA\Property(property: 'items', type: 'array', items: new OA\Items(new Model(type: StreamRequestDTO::class, groups: ['default']))),
                new OA\Property(property: 'total', type: 'integer', example: 100),
                new OA\Property(property: 'page', type: 'integer', example: 1),
                new OA\Property(property: 'limit', type: 'integer', example: 10),
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
class FindStreamController extends \App\Shared\Presentation\Controller\BaseController
{
    public function __construct(
        private \App\Shared\Application\Bus\QueryBus $query_bus,
        private \App\Core\Application\Mapper\Stream\StreamMapperInterface $mapper
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $page = (int) $request->query->get('page', 1);
            $limit = (int) $request->query->get('limit', 10);
            $filters = $request->query->all();
            unset($filters['page'], $filters['limit']);

            $query = new \App\Core\Application\Query\FindStreamPaginatedQuery(
                page: $page,
                limit: $limit,
                filters: $filters
            );

            $result = $this->query_bus->dispatch($query);
            $total = $result['total'] ?? 0;
            $totalPages = $limit > 0 ? (int) ceil($total / $limit) : 0;

            $responseData = [
            'items' => $result['data'] ?? [],
            ];

            $responseData = array_merge($responseData, [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'totalPages' => $totalPages,
            ]);

            return Response::successResponse(
                $responseData
            );
        } catch (\Exception $e) {
            return Response::errorResponse($e->getMessage(), 400);
        }
    }
}
