<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly NormalizerInterface $decorated,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>|string|int|float|bool|\ArrayObject<int|string, mixed>|null
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $violations = null;
        if (method_exists($object, 'getConstraintViolationList')) {
            $violations = $object->getConstraintViolationList();
        }

        $data = $this->decorated->normalize($object, $format, $context);

        if (null !== $violations && $violations instanceof ConstraintViolationListInterface && count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $message = $violation->getMessage();

                if (!isset($errors[$propertyPath])) {
                    $errors[$propertyPath] = [];
                }

                $errors[$propertyPath][] = $message;
            }

            $data['errors'] = $errors;
            $data['detail'] = 'Validation failed';
            if (isset($data['hydra:description'])) {
                unset($data['hydra:description']);
            }

            return $data;
        }

        if (isset($data['violations']) && is_array($data['violations'])) {
            $errors = [];

            foreach ($data['violations'] as $violation) {
                $propertyPath = $violation['propertyPath'] ?? 'general';
                $message = $violation['message'] ?? 'Validation error';

                if (!isset($errors[$propertyPath])) {
                    $errors[$propertyPath] = [];
                }

                $errors[$propertyPath][] = $message;
            }

            $data['errors'] = $errors;
            $data['detail'] = 'Validation failed';
            unset($data['violations']);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->decorated->getSupportedTypes($format);
    }
}
