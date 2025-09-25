# Configuration de l'environnement pour CQRS

## Variables d'environnement requises

Ajoutez ces variables à votre fichier `.env.local` :

```env
# Messenger Configuration
MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
```

## Configuration RabbitMQ

### Installation locale

```bash
# Avec Docker
docker run -d --name rabbitmq -p 5672:5672 -p 15672:15672 rabbitmq:3-management

# Avec Homebrew (macOS)
brew install rabbitmq
brew services start rabbitmq
```

### Configuration de production

Pour la production, utilisez un service RabbitMQ managé ou configurez un cluster RabbitMQ.

## Commandes de setup

```bash
# Créer la migration
make migration

# Exécuter la migration
make migrate

# Setup des transports Messenger
make fabric

# Charger les fixtures
make fixture
```

## Vérification

Pour vérifier que tout fonctionne :

```bash
# Vérifier la configuration Messenger
php bin/console debug:messenger

# Vérifier les transports
php bin/console messenger:setup-transports

# Tester les workers
php bin/console messenger:consume async -vv
```
