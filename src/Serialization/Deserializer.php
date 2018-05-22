<?php


namespace xiio\ObjectSerializer\Serialization;

use Doctrine\Instantiator\Instantiator;
use xiio\ObjectSerializer\Hydration\Hydrator;
use xiio\ObjectSerializer\Mapping\Mapping;

class Deserializer
{

    /**
     * @param array $data
     * @param \xiio\ObjectSerializer\Mapping\Mapping|null $mapping
     *
     * @return array|object
     * @throws \xiio\ObjectSerializer\Exception\MappingNotFoundException
     */
    public static function deserialize(array $data, Mapping $mapping = null)
    {
        if ($mapping->isObject()) {
            $result = static::deserializeData($data, $mapping);
            $object = static::createInstance($mapping->getType());
            $result = Hydrator::populate($result, $object);
        } elseif ($mapping->isArrayOfObjects()) {
            $result = [];
            foreach ($data as $key => $item) {
                $result = static::deserializeData($item, $mapping);
                $object = static::createInstance($mapping->getType());
                $result[$key] = Hydrator::populate($result, $object);
            }
        } else {
            $result = $data;
        }

        return $result;
    }

    /**
     * @param array $data
     * @param \xiio\ObjectSerializer\Mapping\Mapping|null $mapping
     * @param string $parentName
     *
     * @return array
     * @throws \xiio\ObjectSerializer\Exception\MappingNotFoundException
     */
    private static function deserializeData(array $data, Mapping $mapping = null, string $parentName = '')
    {
        $result = [];
        foreach ($data as $fieldName => $item) {
            $fieldPath = $parentName.$fieldName;
            if ($mapping->has($fieldPath)) {
                if ($mapping->isArrayOfObjects($fieldPath)) {
                    $result[$fieldName] = [];
                    foreach ($item as $key => $row) {
                        $object = static::createInstance($mapping->getType($fieldPath));
                        $deserialized = static::deserializeData($row, $mapping, $fieldPath.'.');
                        $result[$fieldName][$key] = Hydrator::populate($deserialized, $object);
                    }
                } elseif ($mapping->isObject($fieldPath)) {
                    $object = static::createInstance($mapping->getType($fieldPath));
                    $deserialzed = static::deserializeData($item, $mapping, $fieldPath.'.');
                    $result[$fieldName] = Hydrator::populate($deserialzed, $object);
                } else {
                    $result[$fieldName] = static::deserializeData($item, $mapping, $fieldPath.'.');
                }
            } else {
                $result[$fieldName] = $item;
            }
        }

        return $result;
    }

    /**
     * @param string $class
     *
     * @return object
     */
    private static function createInstance(string $class)
    {
        return (new Instantiator())->instantiate($class);
    }
}
