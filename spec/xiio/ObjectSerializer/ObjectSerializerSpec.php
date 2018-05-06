<?php

namespace spec\xiio\ObjectSerializer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\xiio\ObjectSerializer\Fixtures\NestedObject;
use spec\xiio\ObjectSerializer\Fixtures\TestObject;
use xiio\ObjectSerializer\Exception\FormatNotFoundException;
use xiio\ObjectSerializer\Mapping\PropertyMapper;
use xiio\ObjectSerializer\Mapping\Type\ObjectProperty;
use xiio\ObjectSerializer\ObjectSerializer;

class ObjectSerializerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ObjectSerializer::class);
    }

    function it_can_serialize_to_array()
    {
        $object = new TestObject();
        $this->serializeToArray($object)->shouldBeArray();
        $this->serializeToArray($object)->shouldHaveCount(4);
        $this->serializeToArray($object)->shouldHaveKeyWithValue('publicField', 'public');
        $this->serializeToArray($object)->shouldHaveKeyWithValue('protectedField', 'protected');
        $this->serializeToArray($object)->shouldHaveKeyWithValue('privateField', 'private');
        $this->serializeToArray($object)->shouldHaveKey('nestedObject');
    }

    function it_can_serialize_to_array_nested_objects()
    {
        $object = new TestObject();
        $this->serializeToArray($object)->shouldHaveKey('nestedObject');
        $this->serializeToArray($object)['nestedObject']->shouldHaveKeyWithValue('subField', 'sub field');
        $this->serializeToArray($object)['nestedObject']->shouldHaveKeyWithValue('subFieldArray', ['a', 'b', 'c']);
    }

    function it_can_deserialize_from_array()
    {
        $array = [
            'publicField' => 'deserialized public',
            'protectedField' => 'deserialized protected',
            'privateField' => 'deserialized private',
        ];
        $this->deserializeArray($array, TestObject::class)->shouldBeObject();
        $this->deserializeArray($array, TestObject::class)->shouldBeAnInstanceOf(TestObject::class);
        $this->deserializeArray($array, TestObject::class)->getPublicField()->shouldBe('deserialized public');
        $this->deserializeArray($array, TestObject::class)->getProtectedField()->shouldBe('deserialized protected');
        $this->deserializeArray($array, TestObject::class)->getPrivateField()->shouldBe('deserialized private');
        $this->deserializeArray($array, TestObject::class)->getNestedObject()->shouldBe(null);
    }

    function it_can_deserialize_from_array_with_mapping(PropertyMapper $mapper, ObjectProperty $mappingType)
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

        $this->deserializeArray($array, TestObject::class, $mapper)
            ->getNestedObject()->shouldBeAnInstanceOf(NestedObject::class);
        $this->deserializeArray($array, TestObject::class, $mapper)
            ->getNestedObject()->getSubField()->shouldBe('deserialized subField');
        $this->deserializeArray($array, TestObject::class, $mapper)
            ->getNestedObject()->getSubFieldArray()->shouldBe(['d', 'e', 'f']);
    }

    function it_can_serialize_to_json()
    {
        $object = new TestObject();
        $this->serializeToJson($object)->shouldBeString();
        $this->serializeToJson($object)->shouldBe('{"publicField":"public","protectedField":"protected","privateField":"private","nestedObject":{"subField":"sub field","subFieldArray":["a","b","c"]}}');
    }

    function it_can_deserialize_from_json()
    {
        $json = '{"publicField":"deserialized public","protectedField":"deserialized protected","privateField":"deserialized private","nestedObject":{"subField":"deserialized sub field","subFieldArray":["d","e","f"]}}';
        $this->deserializeJson($json, TestObject::class)->shouldBeAnInstanceOf(TestObject::class);
        $this->deserializeJson($json, TestObject::class)->getPublicField()->shouldBe('deserialized public');
        $this->deserializeJson($json, TestObject::class)->getProtectedField()->shouldBe('deserialized protected');
        $this->deserializeJson($json, TestObject::class)->getPrivateField()->shouldBe('deserialized private');
        $this->deserializeJson($json, TestObject::class)
            ->getNestedObject()->shouldBeArray();
        $this->deserializeJson($json, TestObject::class)
            ->getNestedObject()['subField']->shouldBe('deserialized sub field');
        $this->deserializeJson($json, TestObject::class)
            ->getNestedObject()['subFieldArray']->shouldBe(['d', 'e', 'f']);
    }

    function it_can_deserialize_from_json_with_mapping(PropertyMapper $mapper, ObjectProperty $mappingType)
    {
        $json = '{"publicField":"deserialized public","protectedField":"deserialized protected","privateField":"deserialized private","nestedObject":{"subField":"deserialized sub field","subFieldArray":["d","e","f"]}}';
        $mapper->has('nestedObject')->willReturn(true);
        $mappingType->getTypeName()->willReturn('class');
        $mappingType->getType()->willReturn(NestedObject::class);
        $mapper->get('nestedObject')->willReturn($mappingType);
        $mapper->has(Argument::any())->willReturn(false);

        $this->deserializeJson($json, TestObject::class, $mapper)
            ->getNestedObject()->shouldBeAnInstanceOf(NestedObject::class);
        $this->deserializeJson($json, TestObject::class, $mapper)
            ->getNestedObject()->getSubField()->shouldBe('deserialized sub field');
        $this->deserializeJson($json, TestObject::class, $mapper)
            ->getNestedObject()->getSubFieldArray()->shouldBe(['d', 'e', 'f']);
    }

    function it_throws_exception_on_unknown_format()
    {
        $this->shouldThrow(FormatNotFoundException::class)->duringDeserialize([], 'exe', TestObject::class);
        $this->shouldThrow(FormatNotFoundException::class)->duringSerialize([], 'exe');
    }
}
