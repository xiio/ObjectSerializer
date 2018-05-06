<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Serialization;

use xiio\ObjectSerializer\Filter\FilterInterface;
use xiio\ObjectSerializer\Mapping\PropertyMapper;

interface SerializerInterface
{
    /**
     * @param $object
     * @param \xiio\ObjectSerializer\Filter\FilterInterface|null $filter
     *
     * @return mixed
     */
    public function serialize($object, FilterInterface $filter = null);

    /**
     * @param $data
     * @param string $class
     * @param \xiio\ObjectSerializer\Mapping\PropertyMapper|null $mapping
     *
     * @return mixed
     */
    public function deserialize($data, string $class, PropertyMapper $mapping = null);

    /**
     * @return string
     */
    public function getFormatName(): string;
}
