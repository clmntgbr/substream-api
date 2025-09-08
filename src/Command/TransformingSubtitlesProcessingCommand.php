<?php

namespace App\Command;

use App\Application\Command\GenerateSubtitlesSuccessCommand;
use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use League\Flysystem\FilesystemOperator;

#[AsCommand(
    name: 'debug:transforming-subtitles-processing',
    description: 'Add a short description for your command',
)]
class TransformingSubtitlesProcessingCommand extends Command
{
    public function __construct(
        private StreamRepository $streamRepository,
        private MessageBusInterface $messageBus,
        private FilesystemOperator $awsStorage,
    )
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->addArgument('streamId', InputArgument::OPTIONAL, 'Stream ID', '9729a410-e822-4026-97a8-f99fb0b92778');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $stream = $this->streamRepository->findOneBy(['id' => $input->getArgument('streamId')]);
        if (null === $stream) {
            $io->error('Stream not found');
            return Command::FAILURE;
        }

        $stream->initStatuses();
        $stream->setStatuses([
            StreamStatusEnum::UPLOADING->value,
            StreamStatusEnum::UPLOADED->value,
            StreamStatusEnum::EXTRACTING_SOUND_PROCESSING->value,
            StreamStatusEnum::EXTRACTED_SOUND->value,
            StreamStatusEnum::GENERATING_SUBTITLES_PROCESSING->value,
        ]);

        $this->uploadSubtitleSrtFile($stream);
        $this->uploadSubtitleSrtFiles($stream);
        $this->uploadAudioFiles($stream);
        $this->uploadVideoFile($stream);

        $this->messageBus->dispatch(new GenerateSubtitlesSuccessCommand(
            streamId: $stream->getId(),
            subtitleSrtFiles: $stream->getSubtitleSrtFiles(),
            subtitleSrtFile: $stream->getSubtitleSrtFile(),
        ));

        $this->streamRepository->save($stream);
        return Command::SUCCESS;
    }

    private function uploadSubtitleSrtFile(Stream $stream): void
    {
        $path = $stream->getId().'/'.$stream->getSubtitleSrtFile();

        $handle = fopen('/app/public/debug/transforming_subtitles_processing/' . $stream->getSubtitleSrtFile(), 'r');

        $this->awsStorage->writeStream($path, $handle, [
            'visibility' => 'public',
        ]);

        if (is_resource($handle)) {
            fclose($handle);
        }
    }

    private function uploadSubtitleSrtFiles(Stream $stream): void
    {
        foreach ($stream->getSubtitleSrtFiles() as $subtitleSrtFile) {
            $path = $stream->getId().'/subtitles/'.$subtitleSrtFile;
            
            $handle = fopen('/app/public/debug/transforming_subtitles_processing/subtitles/' . $subtitleSrtFile, 'r');
            
            $this->awsStorage->writeStream($path, $handle, [
                'visibility' => 'public',
            ]);
    
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    private function uploadAudioFiles(Stream $stream): void
    {
        foreach ($stream->getAudioFiles() as $audioFile) {
            $path = $stream->getId().'/audios/'.$audioFile;
            
            $handle = fopen('/app/public/debug/transforming_subtitles_processing/audios/' . $audioFile, 'r');
            
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
        $handle = fopen('/app/public/debug/transforming_subtitles_processing/' . $stream->getFileName(), 'r');
        
        $this->awsStorage->writeStream($path, $handle, [
            'visibility' => 'public',
        ]);

        if (is_resource($handle)) {
            fclose($handle);
        }
    }
}
