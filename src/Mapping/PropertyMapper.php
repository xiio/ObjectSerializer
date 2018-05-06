<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Mapping;

use xiio\ObjectSerializer\Exception\MappingNotFoundException;
use xiio\ObjectSerializer\Mapping\Type\ArrayOfObjectProperty;
use xiio\ObjectSerializer\Mapping\Type\MappingType;
use xiio\ObjectSerializer\Mapping\Type\ObjectProperty;

class PropertyMapper
{
    /**
     * @var array
     */
    private $mapping;

    /**
     * @param string $fieldName
     * @param \xiio\ObjectSerializer\Mapping\Type\MappingType $type
     */
    public function map(string $fieldName, MappingType $type): void
    {
        $this->mapping[$fieldName] = $type;
    }

    /**
     * @param string $fieldName
     * @param string $class
     */
    public function mapClass(string $fieldName, string $class): void
    {
        $this->map($fieldName, new ObjectProperty($class));
    }

    /**
     * @param string $fieldName
     * @param string $class
     */
    public function mapArray(string $fieldName, string $class): void
    {
        $this->map($fieldName, new ArrayOfObjectProperty($class));
    }

    public function has(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->mapping);
    }

    /**
     * @param string $fieldName
     *
     * @return \xiio\ObjectSerializer\Mapping\Type\MappingType
     * @throws \xiio\ObjectSerializer\Exception\MappingNotFoundException
     */
    public function get(string $fieldName): MappingType
    {
        if (!$this->has($fieldName)) {
            throw new MappingNotFoundException(sprintf("Mapping not found for field %s", $fieldName));
        }

        return $this->mapping[$fieldName];
    }

    /**
     * @param string $fieldName
     *
     * @throws \xiio\ObjectSerializer\Exception\MappingNotFoundException
     */
    public function remove(string $fieldName): void
    {
        if (!$this->has($fieldName)) {
            throw new MappingNotFoundException(sprintf("Mapping not found for field %s", $fieldName));
        }

        unset($this->mapping[$fieldName]);
    }
}
