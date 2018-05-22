<?php


namespace xiio\ObjectSerializer\Mapping\Type;

class MappingTypeFactory
{

    public static function object(string $class): ObjectType
    {
        return new ObjectType($class);
    }

    public static function arrayOfObjects(string $class): ArrayOfObjectsType
    {
        return new ArrayOfObjectsType($class);
    }

    public static function array(): ArrayType
    {
        return new ArrayType();
    }
}
