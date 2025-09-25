<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

// Load environment variables
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env.local');

use App\CQRS\Command\Stream\CreateStreamCommand;
use App\CQRS\Query\Stream\GetStreamQuery;
use App\CQRS\Stamp\SyncStamp;
use App\Entity\Options;
use App\Entity\User;
use App\Kernel;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

// Bootstrap Symfony
$kernel = new Kernel('test', true);
$kernel->boot();
$container = $kernel->getContainer();

// Get the buses
$commandBus = $container->get('command.bus');
$queryBus = $container->get('query.bus');

echo "=== Test de l'architecture CQRS ===\n\n";

// Test 1: Création d'un stream (asynchrone)
echo "1. Test création d'un stream (asynchrone)\n";
try {
    $user = new User();
    $user->setEmail('test@example.com');
    $user->setFirstname('Test');
    $user->setLastname('User');
    
    $options = new Options();
    $options->setSubtitleFont('Arial');
    $options->setSubtitleSize(16);
    $options->setSubtitleColor('#FFFFFF');
    $options->setSubtitleBold(false);
    $options->setSubtitleItalic(false);
    $options->setSubtitleUnderline(false);
    $options->setSubtitleOutlineColor('#000000');
    $options->setSubtitleOutlineThickness(2);
    $options->setSubtitleShadow(1);
    $options->setSubtitleShadowColor('#000000');
    $options->setVideoFormat('mp4');
    $options->setVideoParts(1);
    $options->setYAxisAlignment(0.0);

    $command = new CreateStreamCommand(
        'test-video.mp4',
        'original-test-video.mp4',
        'video/mp4',
        1024000,
        'https://example.com/test-video.mp4',
        $user,
        $options
    );

    $envelope = $commandBus->dispatch($command);
    $handledStamp = $envelope->last(HandledStamp::class);
    
    if ($handledStamp) {
        $jobId = $handledStamp->getResult();
        echo "✅ Commande créée avec succès. Job ID: " . $jobId . "\n";
    } else {
        echo "❌ Échec de la création de la commande\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Création d'un stream (synchrone)
echo "2. Test création d'un stream (synchrone)\n";
try {
    $user = new User();
    $user->setEmail('test-sync@example.com');
    $user->setFirstname('Test');
    $user->setLastname('Sync');
    
    $options = new Options();
    $options->setSubtitleFont('Arial');
    $options->setSubtitleSize(16);
    $options->setSubtitleColor('#FFFFFF');
    $options->setSubtitleBold(false);
    $options->setSubtitleItalic(false);
    $options->setSubtitleUnderline(false);
    $options->setSubtitleOutlineColor('#000000');
    $options->setSubtitleOutlineThickness(2);
    $options->setSubtitleShadow(1);
    $options->setSubtitleShadowColor('#000000');
    $options->setVideoFormat('mp4');
    $options->setVideoParts(1);
    $options->setYAxisAlignment(0.0);

    $command = new CreateStreamCommand(
        'test-sync-video.mp4',
        'original-test-sync-video.mp4',
        'video/mp4',
        2048000,
        'https://example.com/test-sync-video.mp4',
        $user,
        $options
    );

    $envelope = $commandBus->dispatch($command, [new SyncStamp()]);
    $handledStamp = $envelope->last(HandledStamp::class);
    
    if ($handledStamp) {
        $streamId = $handledStamp->getResult();
        echo "✅ Stream créé avec succès (synchrone). Stream ID: " . $streamId . "\n";
        
        // Test 3: Récupération du stream
        echo "\n3. Test récupération du stream\n";
        $query = new GetStreamQuery($streamId);
        $envelope = $queryBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);
        
        if ($handledStamp) {
            $result = $handledStamp->getResult();
            if ($result->isSuccess()) {
                echo "✅ Stream récupéré avec succès\n";
                $stream = $result->getData();
                echo "   - Nom du fichier: " . $stream->getFileName() . "\n";
                echo "   - Taille: " . $stream->getSize() . " bytes\n";
                echo "   - Statut: " . $stream->getStatus() . "\n";
            } else {
                echo "❌ Échec de la récupération: " . $result->getError() . "\n";
            }
        } else {
            echo "❌ Échec de la récupération du stream\n";
        }
    } else {
        echo "❌ Échec de la création du stream (synchrone)\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== Fin des tests ===\n";
