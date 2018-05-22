<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Serialization;

use xiio\ObjectSerializer\Filter\FilterInterface;
use xiio\ObjectSerializer\Hydration\Hydrator;
use xiio\ObjectSerializer\Mapping\Mapping;

final class ArraySerializer implements SerializerInterface
{

    const FORMAT = 'array';

    /**
     * @param $object
     * @param \xiio\ObjectSerializer\Filter\FilterInterface|null $filter
     *
     * @return array
     */
    public function serialize($object, FilterInterface $filter = null): array
    {
        return Hydrator::extract($object, $filter);
    }

    /**
     * @param array $arrayData
     * @param \xiio\ObjectSerializer\Mapping\Mapping|null $mapping
     *
     * @return object
     * @throws \xiio\ObjectSerializer\Exception\MappingNotFoundException
     */
    public function deserialize($arrayData, Mapping $mapping = null)
    {
        return Deserializer::deserialize($arrayData, $mapping);
    }

    public function getFormatName(): string
    {
        return self::FORMAT;
    }
}
