<?php

declare(strict_types=1);

namespace App\CQRS\Command;

interface TrackableCommandInterface extends CommandMessage
{
    // Interface pour les commandes qui nécessitent un suivi via Job
}
