<?php

declare(strict_types=1);

namespace App\Controller;

use App\CQRS\Command\Stream\CreateStreamCommand;
use App\CQRS\Query\Stream\GetStreamQuery;
use App\CQRS\Stamp\SyncStamp;
use App\Entity\Options;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
#[Route('/api/streams')]
#[IsGranted('ROLE_USER')]
class StreamController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'command.bus')]
        private MessageBusInterface $commandBus,
        #[Autowire(service: 'query.bus')]
        private MessageBusInterface $queryBus,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', methods: ['POST'])]
    public function createStream(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Validation des données d'entrée
        $errors = $this->validateCreateStreamData($data);
        if (!empty($errors)) {
            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        // Création de la commande
        $command = new CreateStreamCommand(
            $data['fileName'],
            $data['originalFileName'],
            $data['mimeType'],
            $data['size'],
            $data['url'],
            $this->getUser(),
            $this->createOptionsFromData($data['options'] ?? [])
        );

        // Validation de la commande
        $violations = $this->validator->validate($command);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Dispatch de la commande
            $envelope = $this->commandBus->dispatch($command);
            $handledStamp = $envelope->last(HandledStamp::class);
            
            if ($handledStamp) {
                $jobId = $handledStamp->getResult();
                return new JsonResponse(['jobId' => $jobId], Response::HTTP_ACCEPTED);
            }

            return new JsonResponse(['error' => 'Failed to create stream'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getStream(string $id): JsonResponse
    {
        try {
            $streamId = Uuid::fromString($id);
        } catch (\InvalidArgumentException) {
            return new JsonResponse(['error' => 'Invalid stream ID'], Response::HTTP_BAD_REQUEST);
        }

        $query = new GetStreamQuery($streamId);
        
        // Validation de la requête
        $violations = $this->validator->validate($query);
        if (count($violations) > 0) {
            return new JsonResponse(['error' => 'Invalid query'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Dispatch de la requête
            $envelope = $this->queryBus->dispatch($query);
            $handledStamp = $envelope->last(HandledStamp::class);
            
            if ($handledStamp) {
                $result = $handledStamp->getResult();
                
                if ($result->isFailure()) {
                    return new JsonResponse(['error' => $result->getError()], Response::HTTP_NOT_FOUND);
                }

                return new JsonResponse($result->getData());
            }

            return new JsonResponse(['error' => 'Failed to retrieve stream'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/sync', methods: ['POST'])]
    public function createStreamSync(Request $request, string $id): JsonResponse
    {
        // Exemple d'exécution synchrone pour les cas spéciaux
        $data = json_decode($request->getContent(), true);
        
        $command = new CreateStreamCommand(
            $data['fileName'],
            $data['originalFileName'],
            $data['mimeType'],
            $data['size'],
            $data['url'],
            $this->getUser(),
            $this->createOptionsFromData($data['options'] ?? [])
        );

        try {
            // Force l'exécution synchrone avec le SyncStamp
            $envelope = $this->commandBus->dispatch($command, [new SyncStamp()]);
            $handledStamp = $envelope->last(HandledStamp::class);
            
            if ($handledStamp) {
                $streamId = $handledStamp->getResult();
                return new JsonResponse(['streamId' => $streamId], Response::HTTP_CREATED);
            }

            return new JsonResponse(['error' => 'Failed to create stream'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateCreateStreamData(array $data): array
    {
        $errors = [];
        
        if (empty($data['fileName'])) {
            $errors[] = 'fileName is required';
        }
        
        if (empty($data['originalFileName'])) {
            $errors[] = 'originalFileName is required';
        }
        
        if (empty($data['mimeType'])) {
            $errors[] = 'mimeType is required';
        }
        
        if (!isset($data['size']) || !is_int($data['size']) || $data['size'] <= 0) {
            $errors[] = 'size must be a positive integer';
        }
        
        if (empty($data['url'])) {
            $errors[] = 'url is required';
        }
        
        return $errors;
    }

    private function createOptionsFromData(array $data): Options
    {
        $options = new Options();
        
        // Configuration par défaut ou depuis les données
        $options->setSubtitleFont($data['subtitleFont'] ?? 'Arial');
        $options->setSubtitleSize($data['subtitleSize'] ?? 16);
        $options->setSubtitleColor($data['subtitleColor'] ?? '#FFFFFF');
        $options->setSubtitleBold($data['subtitleBold'] ?? false);
        $options->setSubtitleItalic($data['subtitleItalic'] ?? false);
        $options->setSubtitleUnderline($data['subtitleUnderline'] ?? false);
        $options->setSubtitleOutlineColor($data['subtitleOutlineColor'] ?? '#000000');
        $options->setSubtitleOutlineThickness($data['subtitleOutlineThickness'] ?? 2);
        $options->setSubtitleShadow($data['subtitleShadow'] ?? 1);
        $options->setSubtitleShadowColor($data['subtitleShadowColor'] ?? '#000000');
        $options->setVideoFormat($data['videoFormat'] ?? 'mp4');
        $options->setVideoParts($data['videoParts'] ?? 1);
        $options->setYAxisAlignment($data['yAxisAlignment'] ?? 0.0);
        
        return $options;
    }
}
