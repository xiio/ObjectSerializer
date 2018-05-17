<?php

namespace spec\xiio\ObjectSerializer\Hydration;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\xiio\ObjectSerializer\Fixtures\NestedObject;
use spec\xiio\ObjectSerializer\Fixtures\TestObject;
use xiio\ObjectSerializer\Mapping\PropertyMapper;
use xiio\ObjectSerializer\Mapping\Type\ObjectProperty;

class HydratorSpec extends ObjectBehavior
{
    function it_can_extract_properties_from_object()
    {
        $testObject = new TestObject();
        self::extract($testObject)->shouldBeArray();
        self::extract($testObject)->shouldHaveCount(4);
        self::extract($testObject)->shouldHaveKeyWithValue('publicField', 'public');
        self::extract($testObject)->shouldHaveKeyWithValue('protectedField', 'protected');
        self::extract($testObject)->shouldHaveKeyWithValue('privateField', 'private');
        self::extract($testObject)->shouldHaveKey('nestedObject');
        self::extract($testObject)['nestedObject']->shouldBeArray();
        self::extract($testObject)['nestedObject']->shouldHaveCount(2);
        self::extract($testObject)['nestedObject']->shouldHaveKeyWithValue('subField', 'sub field');
        self::extract($testObject)['nestedObject']->shouldHaveKeyWithValue('subFieldArray', ['a', 'b', 'c']);
    }

    function it_can_populate_array_to_object(PropertyMapper $mapper, ObjectProperty $mappingType)
    {
        $array = [
            'nestedObject' => [
                'subField' => 'deserialized subField',
                'subFieldArray' => ['d', 'e', 'f'],
            ],
        ];
        $mapper->has('nestedObject')->willReturn(true);
        $mappingType->getTypeName()->willReturn('class');
        $mappingType->getType()->willReturn(NestedObject::class);
        $mapper->get('nestedObject')->willReturn($mappingType);
        $mapper->has(Argument::any())->willReturn(false);
        $object = new TestObject();

        self::populate($array, $object, $mapper)
            ->getNestedObject()->shouldBeAnInstanceOf(NestedObject::class);
        self::populate($array, $object, $mapper)
            ->getNestedObject()->getSubField()->shouldBe('deserialized subField');
        self::populate($array, $object, $mapper)
            ->getNestedObject()->getSubFieldArray()->shouldBe(['d', 'e', 'f']);
    }

    function it_can_deserialize_array_to_object(PropertyMapper $mapper, ObjectProperty $mappingType)
    {
        $array = [
            'nestedObject' => [
                'subField' => 'deserialized subField',
                'subFieldArray' => ['d', 'e', 'f'],
            ],
        ];
        $mapper->has('nestedObject')->willReturn(true);
        $mappingType->getTypeName()->willReturn('class');
        $mappingType->getType()->willReturn(NestedObject::class);
        $mapper->get('nestedObject')->willReturn($mappingType);
        $mapper->has(Argument::any())->willReturn(false);

        self::deserialize($array, TestObject::class, $mapper)
            ->getNestedObject()->shouldBeAnInstanceOf(NestedObject::class);
        self::deserialize($array, TestObject::class, $mapper)
            ->getNestedObject()->getSubField()->shouldBe('deserialized subField');
        self::deserialize($array, TestObject::class, $mapper)
            ->getNestedObject()->getSubFieldArray()->shouldBe(['d', 'e', 'f']);
    }
}
