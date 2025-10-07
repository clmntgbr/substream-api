<?php

namespace App\Command;

use App\Core\Application\Command\TransformSubtitleCommand;
use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use App\Repository\StreamRepository;
use App\Repository\UserRepository;
use App\Service\UploadFileServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Doctrine\Common\Collections\ArrayCollection;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
        private FilesystemOperator $awsStorage,
        private UploadFileServiceInterface $uploadFileService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
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

        $stream->setTasks(new ArrayCollection());
        $stream->setSubtitleAssFileName(null);
        $stream->setChunkFileNames([]);
        $stream->setResizeFileName(null);
        $stream->setEmbedFileName(null);
        $stream->setStatus(StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED->value);
        $stream->setStatuses([StreamStatusEnum::CREATED->value, StreamStatusEnum::UPLOADED->value, StreamStatusEnum::EXTRACTING_SOUND->value, StreamStatusEnum::EXTRACTING_SOUND_COMPLETED->value, StreamStatusEnum::GENERATING_SUBTITLE->value, StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED->value]);

        $this->uploadFileService->deleteAllFiles($stream->getId());
        $this->uploadSubtitleSrtFile($stream);
        $this->uploadAudioFiles($stream);
        $this->uploadVideoFile($stream);

        $this->streamRepository->save($stream);

        return $stream;
    }

    private function uploadSubtitleSrtFile(Stream $stream): void
    {
        $path = $stream->getId().'/subtitles/'.$stream->getSubtitleSrtFileName();

        $handle = fopen('/app/public/debug/'.$stream->getSubtitleSrtFileName(), 'r');

        $this->awsStorage->writeStream($path, $handle, [
            'visibility' => 'public',
        ]);

        if (is_resource($handle)) {
            fclose($handle);
        }
    }

    private function uploadAudioFiles(Stream $stream): void
    {
        foreach ($stream->getAudioFiles() as $audioFile) {
            $path = $stream->getId().'/audios/'.$audioFile;

            $handle = fopen('/app/public/debug/'.$audioFile, 'r');

            $this->awsStorage->writeStream($path, $handle, [
                'visibility' => 'public',
            ]);

            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    private function uploadVideoFile(Stream $stream): void
    {
        $path = $stream->getId().'/'.$stream->getFileName();
        $handle = fopen('/app/public/debug/'.$stream->getFileName(), 'r');

        $this->awsStorage->writeStream($path, $handle, [
            'visibility' => 'public',
        ]);

        if (is_resource($handle)) {
            fclose($handle);
        }
    }
}
