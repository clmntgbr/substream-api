<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Application\Command\ResumeVideoCommand;
use App\Core\Domain\Stream\Entity\Stream;
use App\Core\Domain\Stream\Enum\StreamStatusEnum;
use App\Core\Domain\Stream\Repository\StreamRepository;
use App\Service\S3ServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Doctrine\Common\Collections\ArrayCollection;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'resume-video',
    description: 'Resume video',
)]
class DispatchResumeVideoCommand extends Command
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private FilesystemOperator $awsStorage,
        private S3ServiceInterface $s3Service,
        private KernelInterface $kernel,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->loadFixtures();
        $stream = $this->init();

        $subtitleSrtFileName = $stream->getSubtitleSrtFileName();
        if (null === $subtitleSrtFileName) {
            throw new \RuntimeException('Subtitle SRT file name is required');
        }

        $this->commandBus->dispatch(new ResumeVideoCommand(
            streamId: $stream->getId(),
            subtitleSrtFileName: $subtitleSrtFileName,
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
        $stream->setSubtitleAssFileName('1bba6dc7-21ed-41c2-9694-6a2ea4db41fd.ass');
        $stream->setChunkFileNames([
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_001.mp4',
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_002.mp4',
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_003.mp4',
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_004.mp4',
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_005.mp4',
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_006.mp4',
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_007.mp4',
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_008.mp4',
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_009.mp4',
            '1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_010.mp4',
        ]);
        $stream->setResizeFileName('1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_resize.mp4');
        $stream->setEmbedFileName('1bba6dc7-21ed-41c2-9694-6a2ea4db41fd_embed.mp4');
        $stream->setStatus(StreamStatusEnum::CHUNKING_VIDEO_COMPLETED->value);
        $stream->setStatuses([
            StreamStatusEnum::CREATED->value,
            StreamStatusEnum::UPLOADED->value,
            StreamStatusEnum::EXTRACTING_SOUND->value,
            StreamStatusEnum::EXTRACTING_SOUND_COMPLETED->value,
            StreamStatusEnum::GENERATING_SUBTITLE->value,
            StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED->value,
            StreamStatusEnum::TRANSFORMING_SUBTITLE->value,
            StreamStatusEnum::TRANSFORMING_SUBTITLE_COMPLETED->value,
            StreamStatusEnum::RESIZING_VIDEO->value,
            StreamStatusEnum::RESIZING_VIDEO_COMPLETED->value,
            StreamStatusEnum::EMBEDDING_VIDEO->value,
            StreamStatusEnum::EMBEDDING_VIDEO_COMPLETED->value,
            StreamStatusEnum::CHUNKING_VIDEO->value,
            StreamStatusEnum::CHUNKING_VIDEO_COMPLETED->value,
        ]);

        $this->s3Service->deleteAll($stream->getId());

        $this->uploadSubtitleSrtFile($stream);
        $this->uploadSubtitleAssFile($stream);
        $this->uploadAudioFiles($stream);
        $this->uploadVideoFile($stream);
        $this->uploadChunkFiles($stream);
        $this->uploadResizeFile($stream);
        $this->uploadEmbedFile($stream);

        $this->streamRepository->saveAndFlush($stream);

        return $stream;
    }

    private function loadFixtures(): void
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'hautelook:fixtures:load',
            '--no-interaction' => true,
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);
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

    private function uploadSubtitleAssFile(Stream $stream): void
    {
        $path = $stream->getId().'/subtitles/'.$stream->getSubtitleAssFileName();

        $handle = fopen('/app/public/debug/'.$stream->getSubtitleAssFileName(), 'r');

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

    private function uploadChunkFiles(Stream $stream): void
    {
        $chunkFileNames = $stream->getChunkFileNames();
        if (null === $chunkFileNames) {
            return;
        }

        foreach ($chunkFileNames as $chunkFileName) {
            $path = $stream->getId().'/'.$chunkFileName;
            $handle = fopen('/app/public/debug/'.$chunkFileName, 'r');

            $this->awsStorage->writeStream($path, $handle, [
                'visibility' => 'public',
            ]);

            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    private function uploadResizeFile(Stream $stream): void
    {
        $path = $stream->getId().'/'.$stream->getResizeFileName();
        $handle = fopen('/app/public/debug/'.$stream->getResizeFileName(), 'r');

        $this->awsStorage->writeStream($path, $handle, [
            'visibility' => 'public',
        ]);
    }

    private function uploadEmbedFile(Stream $stream): void
    {
        $path = $stream->getId().'/'.$stream->getEmbedFileName();
        $handle = fopen('/app/public/debug/'.$stream->getEmbedFileName(), 'r');

        $this->awsStorage->writeStream($path, $handle, [
            'visibility' => 'public',
        ]);
    }
}
