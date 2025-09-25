# Architecture CQRS avec Symfony Messenger

## Vue d'ensemble

Cette implémentation suit le pattern CQRS (Command Query Responsibility Segregation) avec Symfony Messenger pour séparer clairement les opérations de lecture (Query) et d'écriture (Command).

## Structure

### Commandes (Modifications d'état)
- **Interface**: `CommandMessage`
- **Interface de suivi**: `TrackableCommandInterface`
- **Bus**: `command.bus`
- **Transport**: Asynchrone par défaut (RabbitMQ)
- **Exécution synchrone**: Possible avec `SyncStamp`

### Requêtes (Lecture de données)
- **Interface**: `QueryMessage`
- **Bus**: `query.bus`
- **Transport**: Synchrone
- **Retour**: `QueryResult`

## Utilisation

### Créer une commande

```php
// 1. Créer la commande
class CreateStreamCommand implements TrackableCommandInterface
{
    public function __construct(
        public readonly string $fileName,
        public readonly User $user
    ) {}
}

// 2. Créer le handler
#[AsMessageHandler]
class CreateStreamCommandHandler
{
    public function __invoke(CreateStreamCommand $command): Uuid
    {
        // Logique métier
        return $stream->getId();
    }
}

// 3. Dispatcher depuis le contrôleur
$envelope = $this->commandBus->dispatch($command);
$jobId = $envelope->last(HandledStamp::class)->getResult();
```

### Créer une requête

```php
// 1. Créer la requête
class GetStreamQuery implements QueryMessage
{
    public function __construct(
        public readonly Uuid $streamId
    ) {}
}

// 2. Créer le handler
#[AsMessageHandler]
class GetStreamQueryHandler
{
    public function __invoke(GetStreamQuery $query): QueryResult
    {
        $stream = $this->streamService->getStreamById($query->streamId);
        return new QueryResult($stream);
    }
}

// 3. Dispatcher depuis le contrôleur
$envelope = $this->queryBus->dispatch($query);
$result = $envelope->last(HandledStamp::class)->getResult();
```

## Suivi des commandes

Les commandes implémentant `TrackableCommandInterface` créent automatiquement un `Job` pour le suivi :

```php
// Statuts disponibles
enum JobStatusEnum: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case SUCCESS = 'success';
    case FAILURE = 'failure';
}
```

## Exécution synchrone

Pour forcer l'exécution synchrone d'une commande :

```php
$envelope = $this->commandBus->dispatch($command, [new SyncStamp()]);
```

## Middlewares

### CreateJobMiddleware
- Crée un Job pour les commandes trackables
- Retourne le JobId au contrôleur

### SyncExecutionMiddleware
- Court-circuite le transport asynchrone si `SyncStamp` présent

### BusinessExceptionMiddleware
- Gère les exceptions métier
- Évite les retries inutiles pour les erreurs non-récupérables

## Avantages

1. **Séparation claire** : Lecture vs écriture
2. **Traçabilité** : Suivi complet des commandes asynchrones
3. **Testabilité** : Handlers isolés et testables
4. **Scalabilité** : Prêt pour la montée en charge
5. **Flexibilité** : Exécution synchrone ou asynchrone selon les besoins

## Configuration

### Variables d'environnement

```env
MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
```

### Migration

Exécuter la migration pour créer la table Job :

```bash
make migrate
```

### Setup RabbitMQ

```bash
make fabric
```

## Exemples d'utilisation

### Créer un stream (asynchrone)

```bash
curl -X POST http://localhost/api/streams \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "fileName": "video.mp4",
    "originalFileName": "original_video.mp4",
    "mimeType": "video/mp4",
    "size": 1024000,
    "url": "https://example.com/video.mp4",
    "options": {
      "subtitleFont": "Arial",
      "subtitleSize": 16,
      "subtitleColor": "#FFFFFF"
    }
  }'
```

### Récupérer un stream

```bash
curl -X GET http://localhost/api/streams/{streamId} \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Créer un stream (synchrone)

```bash
curl -X POST http://localhost/api/streams/{id}/sync \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "fileName": "video.mp4",
    "originalFileName": "original_video.mp4",
    "mimeType": "video/mp4",
    "size": 1024000,
    "url": "https://example.com/video.mp4"
  }'
```
