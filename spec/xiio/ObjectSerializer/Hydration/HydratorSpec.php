<?php

namespace spec\xiio\ObjectSerializer\Hydration;

use PhpSpec\ObjectBehavior;
use spec\xiio\ObjectSerializer\Fixtures\TestObject;

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

    function it_can_populate_array_to_object()
    {
        $array = [
            'publicField' => 'publicFieldVal',
            'protectedField' => 'protectedFieldVal',
            'privateField' => 'privateFieldVal',
            'nestedObject' => [],
        ];
        $object = new TestObject();

        self::populate($array, $object)
            ->getPublicField()->shouldReturn('publicFieldVal');
        self::populate($array, $object)
            ->getProtectedField()->shouldReturn('protectedFieldVal');
        self::populate($array, $object)
            ->getPrivateField()->shouldReturn('privateFieldVal');
        self::populate($array, $object)
            ->getNestedObject()->shouldReturn([]);
    }
//
//    function it_can_deserialize_array_to_object(Mapping $mapper, ObjectType $mappingType)
//    {
//        $array = [
//            'nestedObject' => [
//                'subField' => 'deserialized subField',
//                'subFieldArray' => ['d', 'e', 'f'],
//            ],
//        ];
//        $mappingType->getType()->willReturn(NestedObject::class);
//        $mappingType->isArrayOfType()->willReturn(false);
//        $mapper->has('nestedObject')->willReturn(true);
//        $mapper->has(Argument::any())->willReturn(false);
//        $mapper->getType('nestedObject')->willReturn($mappingType);
//        $mapper->has("nestedObject")->willReturn(false);
//
//        self::deserialize($array, TestObject::class, $mapper)
//            ->getNestedObject()->shouldBeAnInstanceOf(NestedObject::class);
//        self::deserialize($array, TestObject::class, $mapper)
//            ->getNestedObject()->getSubField()->shouldBe('deserialized subField');
//        self::deserialize($array, TestObject::class, $mapper)
//            ->getNestedObject()->getSubFieldArray()->shouldBe(['d', 'e', 'f']);
//    }
}
