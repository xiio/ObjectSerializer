<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Mapping;

use xiio\ObjectSerializer\Exception\MappingNotFoundException;
use xiio\ObjectSerializer\Mapping\Type\ArrayOfObjectsType;
use xiio\ObjectSerializer\Mapping\Type\ArrayType;
use xiio\ObjectSerializer\Mapping\Type\MappingType;
use xiio\ObjectSerializer\Mapping\Type\MappingTypeFactory;
use xiio\ObjectSerializer\Mapping\Type\ObjectType;

class Mapping
{

    /**
     * @var \xiio\ObjectSerializer\Mapping\Type\MappingType[]
     */
    private $mapping = [];

    /**
     * @param \xiio\ObjectSerializer\Mapping\Type\MappingType $rootType
     */
    public function __construct(MappingType $rootType)
    {
        $this->mapping[''] = $rootType;
    }

    /**
     * @param string $fieldName
     * @param \xiio\ObjectSerializer\Mapping\Type\MappingType $type
     */
    public function map(string $fieldName, MappingType $type): void
    {
        if (strpos($fieldName, '.') !== false) {
            $path = [];
            foreach (explode('.', $fieldName) as $part) {
                $path[] = $part;
                $partialPath = implode('.', $path);
                if (!$this->has($partialPath)) {
                    $this->mapping[$partialPath] = null;
                }
            }
        }
        $this->mapping[$fieldName] = $type;
    }

    /**
     * @param string $fieldName
     * @param string $class
     */
    public function mapClass(string $fieldName, string $class): void
    {
        $this->map($fieldName, MappingTypeFactory::object($class));
    }

    /**
     * @param string $fieldName
     * @param string $class
     */
    public function mapArray(string $fieldName, string $class): void
    {
        $this->map($fieldName, MappingTypeFactory::arrayOfObjects($class));
    }

    public function has(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->mapping);
    }

    /**
     * @param string $fieldName If empty return value for root
     *
     * @return \xiio\ObjectSerializer\Mapping\Type\MappingType
     * @throws \xiio\ObjectSerializer\Exception\MappingNotFoundException
     */
    public function getType(string $fieldName = ''): string
    {
        if (!$this->has($fieldName)) {
            throw new MappingNotFoundException(sprintf("Mapping not found for field %s", $fieldName));
        }

        return $this->mapping[$fieldName]->getType() ?? MappingTypeFactory::array()->getType();
    }

    /**
     * @param string $fieldName If empty return value for root
     *
     * @return bool
     */
    public function isArrayOfObjects(string $fieldName = ''): bool
    {
        return $this->mapping[$fieldName] instanceof ArrayOfObjectsType;
    }

    /**
     * @param string $fieldName If empty return value for root
     *
     * @return bool
     */
    public function isArray(string $fieldName = ''): bool
    {
        return $this->mapping[$fieldName] instanceof ArrayType;
    }

    /**
     * @param string $fieldName If empty return value for root
     *
     * @return bool
     */
    public function isObject(string $fieldName = ''): bool
    {
        return $this->mapping[$fieldName] instanceof ObjectType;
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
