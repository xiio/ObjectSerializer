<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Mapping\Type;

class ArrayOfObjectProperty extends ObjectProperty
{
    const TYPE = 'class_array';

    public function getTypeName(): string
    {
        return static::TYPE;
    }
}
