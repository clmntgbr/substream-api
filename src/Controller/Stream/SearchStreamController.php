<?php

declare(strict_types=1);

namespace App\Controller\Stream;

use App\Dto\SearchRequestDto;
use App\Entity\User;
use App\Repository\ElasticaStreamRepository;
use App\SearchDecorator\SearchDecorator;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
class SearchStreamController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ElasticaStreamRepository $elasticaStreamRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function __invoke(
        #[CurrentUser] User $user,
        #[MapQueryString()] SearchRequestDto $searchRequest,
        Request $request,
    ): JsonResponse {
        $parameters = $request->query->all();
        $search = new SearchDecorator($parameters);

        $response = $this->elasticaStreamRepository->search(
            $search->getSearch(),
            $searchRequest->page,
            $searchRequest->itemsPerPage,
            $user
        );

        $normalizedResponse = $this->normalizer->normalize($response, null, ['groups' => ['stream:read', 'option:read']]);

        return new JsonResponse($normalizedResponse, JsonResponse::HTTP_OK);
    }
}
