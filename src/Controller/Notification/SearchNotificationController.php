<?php

namespace App\Controller\Notification;

use App\Dto\SearchRequestDto;
use App\Entity\User;
use App\Repository\ElasticaNotificationRepository;
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
class SearchNotificationController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ElasticaNotificationRepository $elasticaNotificationRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function __invoke(
        #[CurrentUser] User $user,
        #[MapQueryString()] SearchRequestDto $searchRequest,
        Request $request,
    ) {
        $parameters = $request->query->all();
        $search = new SearchDecorator($parameters);

        $response = $this->elasticaNotificationRepository->search(
            $user,
            $search->getSearch(),
            $searchRequest->page,
            $searchRequest->itemsPerPage
        );

        $normalizedResponse = $this->normalizer->normalize($response, null, ['groups' => ['notification:read']]);

        return new JsonResponse($normalizedResponse, JsonResponse::HTTP_OK);
    }
}
