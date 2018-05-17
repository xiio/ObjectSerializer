<?php

namespace xiio\ObjectSerializer\Mapping;

use xiio\ObjectSerializer\Exception\MappingTypeNotFoundException;
use xiio\ObjectSerializer\Hydration\Hydrator;
use xiio\ObjectSerializer\Mapping\Type\MappingType;

class MappingResolver
{
    /**
     * @param \xiio\ObjectSerializer\Mapping\Type\MappingType $type
     * @param $value
     *
     * @return array|object
     * @throws \xiio\ObjectSerializer\Exception\MappingTypeNotFoundException
     */
    public static function resolveType(MappingType $type, $value)
    {
        switch ($type->getTypeName()) {
            case 'class':
                return Hydrator::deserialize($value, $type->getType());
            case 'class_array':
                $result = [];
                foreach ($value as $key => $row) {
                    $result[$key] = Hydrator::deserialize($row, $type->getType());
                }

                return $result;
            default:
                throw new MappingTypeNotFoundException(sprintf("Unknown mapping type: %s", $type->getTypeName()));
        }
    }
}
