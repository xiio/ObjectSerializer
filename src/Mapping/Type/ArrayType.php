<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Mapping\Type;

class ArrayType implements MappingType
{
    public function getType(): string
    {
        return 'array';
    }
}
