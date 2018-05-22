<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Serialization;

use xiio\ObjectSerializer\Filter\FilterInterface;
use xiio\ObjectSerializer\Mapping\Mapping;

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
     * @param \xiio\ObjectSerializer\Mapping\Mapping|null $mapping
     *
     * @return mixed
     */
    public function deserialize($data, Mapping $mapping = null);

    /**
     * @return string
     */
    public function getFormatName(): string;
}
