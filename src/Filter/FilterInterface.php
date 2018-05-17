<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Filter;

interface FilterInterface
{
    /**
     * @param $objectData
     *
     * @return array filtered object data
     */
    public function filter(array $objectData): array;

    /**
     * Check if givent object is supported by filter
     * @param $object
     *
     * @return bool
     */
    public function supports($object): bool;
}
