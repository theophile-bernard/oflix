<?php

declare (strict_types = 1);

namespace App\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Entity normalizer
 * REFER : https://gist.github.com/benlac/c9efc733ee16ebd0d438119bcccb92b9
 */
class EntityNormalizer implements DenormalizerInterface 
{
    public function __construct(
        protected EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return strpos($type, 'App\\Entity\\') === 0 && (is_numeric($data));
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->em->find($class, $data);
    }

    /**
     * @inheritDoc
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => true,             // support any classes or interfaces
            '*' => true,                 // Supports any other types
        ];
    }
}