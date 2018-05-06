<?php

namespace spec\xiio\ObjectSerializer\Mapping;

use PhpSpec\ObjectBehavior;
use spec\xiio\ObjectSerializer\Fixtures\Address;
use xiio\ObjectSerializer\Exception\ClassNotFoundException;
use xiio\ObjectSerializer\Exception\MappingNotFoundException;
use xiio\ObjectSerializer\Mapping\PropertyMapper;
use xiio\ObjectSerializer\Mapping\Type\ObjectProperty;

class PropertyMapperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PropertyMapper::class);
    }

    function it_can_map_fields_to_classes()
    {
        $this->mapClass('std_class', \stdClass::class);
        $this->mapClass('address', Address::class);
        $this->shouldThrow(ClassNotFoundException::class)->duringMapClass('address', \NonExistentClass::class);

        $this->has('std_class')->shouldReturn(true);
        $this->has('address')->shouldReturn(true);
        $this->has('test_field')->shouldReturn(false);

        $this->get('std_class')->shouldBeAnInstanceOf(ObjectProperty::class);
        $this->get('std_class')->getType()->shouldReturn(\stdClass::class);
        $this->get('address')->getType()->shouldReturn(Address::class);
        $this->shouldThrow(MappingNotFoundException::class)->duringGet('test_field');
    }
}

