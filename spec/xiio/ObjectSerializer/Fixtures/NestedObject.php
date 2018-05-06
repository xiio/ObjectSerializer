<?php

namespace spec\xiio\ObjectSerializer\Fixtures;

class NestedObject
{
    private $subField = 'sub field';
    private $subFieldArray = ['a', 'b', 'c'];

    /**
     * @return string
     */
    public function getSubField(): string
    {
        return $this->subField;
    }

    /**
     * @return array
     */
    public function getSubFieldArray(): array
    {
        return $this->subFieldArray;
    }
}
