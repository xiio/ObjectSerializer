<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Filter;

interface FilterInterface
{
    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function isExcluded(string $fieldName): bool;
}
