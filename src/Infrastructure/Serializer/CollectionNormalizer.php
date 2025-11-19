<?php

namespace App\Infrastructure\Serializer;

use ApiPlatform\State\Pagination\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'COLLECTION_NORMALIZER_ALREADY_CALLED';

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        // ⚠️ IMPORTANT : Marquer le contexte AVANT d'appeler le normalizer
        $context[self::ALREADY_CALLED] = true;

        // Appeler le normalizer suivant dans la chaîne
        $data = $this->normalizer->normalize($object, $format, $context);

        // Ajouter les infos de pagination
        if ($object instanceof PaginatorInterface && is_array($data)) {
            $currentPage = $object->getCurrentPage();
            $itemsPerPage = $object->getItemsPerPage();
            $totalItems = $object->getTotalItems();
            $totalPages = $itemsPerPage > 0 ? (int) ceil($totalItems / $itemsPerPage) : 1;

            // Ajouter au début du tableau
            $data = array_merge([
                'currentPage' => $currentPage,
                'itemsPerPage' => $itemsPerPage,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
            ], $data);
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        // ⚠️ CRUCIAL : Vérifier si déjà appelé
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof PaginatorInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            PaginatorInterface::class => false, // ⚠️ Mettre false, pas true
        ];
    }
}
