<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Serialization;

use xiio\ObjectSerializer\Filter\FilterInterface;
use xiio\ObjectSerializer\Hydration\Hydrator;
use xiio\ObjectSerializer\Mapping\PropertyMapper;

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
     * @param string $class
     * @param \xiio\ObjectSerializer\Mapping\PropertyMapper|null $mapping
     *
     * @return object
     */
    public function deserialize($arrayData, string $class, PropertyMapper $mapping = null)
    {
        return Hydrator::deserialize($arrayData, $class, $mapping);
    }

    public function getFormatName(): string
    {
        return self::FORMAT;
    }
}
