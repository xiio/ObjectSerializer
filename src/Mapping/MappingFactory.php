<?php


namespace xiio\ObjectSerializer\Mapping;

use xiio\ObjectSerializer\Mapping\Type\MappingTypeFactory;

class MappingFactory
{
    /**
     * @return \xiio\ObjectSerializer\Mapping\Mapping
     */
    public static function createArrayMapping()
    {
        return new Mapping(MappingTypeFactory::array());
    }

    /**
     * @param string $objectClass
     *
     * @return \xiio\ObjectSerializer\Mapping\Mapping
     */
    public static function createObjectMapping(string $objectClass)
    {
        return new Mapping(MappingTypeFactory::object($objectClass));
    }

    /**
     * @param string $objectsClass
     *
     * @return \xiio\ObjectSerializer\Mapping\Mapping
     */
    public static function createArrayOfObjectMapping(string $objectsClass)
    {
        return new Mapping(MappingTypeFactory::arrayOfObjects($objectsClass));
    }
}
