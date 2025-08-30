<?php

namespace App\Service;

use App\Application\Command\CreateClipFromUrl;
use App\Application\Command\CreateClipFromVideo;
use App\Entity\User;
use App\Model\UploadVideoConfiguration;
use App\Model\UploadVideoUrl;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface UploadVideoServiceInterface
{
    public function upload(UploadedFile $file): void;
    public function uploadByUrl(string $url): void;
}
