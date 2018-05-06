<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Filter;

class PropertyFilter implements FilterInterface
{
    /**
     * @var array
     */
    private $fields = [];

    /**
     * @param string $fieldName
     * @param bool $isFiltered Field should be filtered or not
     */
    public function setField(string $fieldName, bool $isFiltered = true): void
    {
        $this->fields[$fieldName] = $isFiltered;
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function hasField(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->fields);
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function isExcluded(string $fieldName): bool
    {
        return $this->hasField($fieldName) && $this->fields[$fieldName] === true;
    }
}
