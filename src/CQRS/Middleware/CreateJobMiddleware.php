<?php

declare(strict_types=1);

namespace App\CQRS\Middleware;

use App\CQRS\Command\TrackableCommandInterface;
use App\CQRS\Service\JobContextService;
use App\CQRS\Stamp\JobIdStamp;
use App\Entity\Job;
use App\Enum\JobStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CreateJobMiddleware implements MiddlewareInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JobContextService $jobContextService
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        // Vérifier si c'est une commande trackable
        if ($message instanceof TrackableCommandInterface) {
            $job = new Job();
            $job->setStatus(JobStatusEnum::PENDING);
            $job->setCommandClass(get_class($message));
            $job->setCommandData($this->serializeMessage($message));

            $this->entityManager->persist($job);
            $this->entityManager->flush();

            // Définir le JobId dans le contexte pour que le handler puisse le récupérer
            $this->jobContextService->setCurrentJobId($job->getId());

            // Ajouter le JobId au message pour que le handler puisse le mettre à jour
            $envelope = $envelope->with(new JobIdStamp($job->getId()));
        }

        $envelope = $stack->next()->handle($envelope, $stack);

        // Si c'est une commande trackable, retourner le JobId
        if ($message instanceof TrackableCommandInterface) {
            $handledStamp = $envelope->last(HandledStamp::class);
            if ($handledStamp) {
                $jobId = $envelope->last(JobIdStamp::class)?->getJobId();
                $envelope = $envelope->with(new HandledStamp($jobId, $handledStamp->getHandlerName()));
            }
            
            // Nettoyer le contexte après traitement
            $this->jobContextService->clearCurrentJobId();
        }

        return $envelope;
    }

    private function serializeMessage(object $message): array
    {
        // Sérialisation simple des propriétés publiques
        $reflection = new \ReflectionClass($message);
        $data = [];
        
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $data[$property->getName()] = $property->getValue($message);
        }
        
        return $data;
    }
}
