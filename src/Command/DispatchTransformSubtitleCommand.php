<?php

namespace App\Command;

use App\Core\Application\Command\TransformSubtitleCommand;
use App\Entity\Stream;
use App\Entity\User;
use App\Enum\StreamStatusEnum;
use App\Repository\UserRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Repository\StreamRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'transform-subtitle',
    description: 'Transform subtitle',
)]
class DispatchTransformSubtitleCommand extends Command
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stream = $this->init();

        $this->commandBus->dispatch(new TransformSubtitleCommand(
            streamId: $stream->getId(),
            subtitleSrtFileName: $stream->getSubtitleSrtFileName(),
        ));

        return Command::SUCCESS;
    }

    private function init(): Stream
    {
        $stream = $this->streamRepository->findByUuid(Uuid::fromString('1bba6dc7-21ed-41c2-9694-6a2ea4db41fd'));

        if (null === $stream) {
            throw new \Exception('Stream not found');
        }

        $stream->setSubtitleAssFileName(null);
        $stream->setStatus(StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED->value);
        $stream->setStatuses([StreamStatusEnum::CREATED->value, StreamStatusEnum::UPLOADED->value, StreamStatusEnum::EXTRACTING_SOUND->value, StreamStatusEnum::EXTRACTING_SOUND_COMPLETED->value, StreamStatusEnum::GENERATING_SUBTITLE->value, StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED->value]);

        $this->streamRepository->save($stream);
        return $stream;
    }
}
