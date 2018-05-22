<?php

namespace spec\xiio\ObjectSerializer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\xiio\ObjectSerializer\Fixtures\NestedObject;
use spec\xiio\ObjectSerializer\Fixtures\Order;
use spec\xiio\ObjectSerializer\Fixtures\OrderItem;
use spec\xiio\ObjectSerializer\Fixtures\OrderItemPrice;
use spec\xiio\ObjectSerializer\Fixtures\TestObject;
use xiio\ObjectSerializer\Exception\FormatNotFoundException;
use xiio\ObjectSerializer\Mapping\Mapping;
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

    function it_can_deserialize_from_array(Mapping $mapping)
    {
        $array = [
            'publicField' => 'deserialized public',
            'protectedField' => 'deserialized protected',
            'privateField' => 'deserialized private',
        ];

        $mapping->has(Argument::any())->willReturn(false);
        $mapping->isObject()->willReturn(true);
        $mapping->getType()->willReturn(TestObject::class);

        $this->deserializeArray($array, $mapping)->shouldBeObject();
        $this->deserializeArray($array, $mapping)->shouldBeAnInstanceOf(TestObject::class);
        $this->deserializeArray($array, $mapping)->getPublicField()->shouldBe('deserialized public');
        $this->deserializeArray($array, $mapping)->getProtectedField()->shouldBe('deserialized protected');
        $this->deserializeArray($array, $mapping)->getPrivateField()->shouldBe('deserialized private');
        $this->deserializeArray($array, $mapping)->getNestedObject()->shouldBe(null);
    }

    function it_can_deserialize_deep_nested_array(Mapping $mapping)
    {
        $array = [
            'id' => 'identity',
            'items' => [
                ['price' => ['price' => 55, 'currency' => 'EUR']],
                ['price' => ['price' => 22, 'currency' => 'PLN']],
            ],
        ];

        $mapping->isObject()->willReturn(true);
        $mapping->getType()->willReturn(Order::class);
        $mapping->has('items')->willReturn(true);
        $mapping->getType('items')->willReturn(OrderItem::class);
        $mapping->isArrayOfObjects('items')->willReturn(true);

        $mapping->getType('items.price')->willReturn(OrderItemPrice::class);
        $mapping->has('items.price')->willReturn(true);
        $mapping->isArrayOfObjects('items.price')->willReturn(false);
        $mapping->isObject('items.price')->willReturn(true);

        $mapping->has(Argument::any())->willReturn(false);

        $this->deserializeArray($array, $mapping)->shouldBeAnInstanceOf(Order::class);
        $this->deserializeArray($array, $mapping)->getId()->shouldReturn('identity');
        $this->deserializeArray($array, $mapping)->getItems()->shouldHaveCount(2);

        $this->deserializeArray($array, $mapping)->getItems()[0]->shouldBeAnInstanceOf(OrderItem::class);
        $this->deserializeArray($array,
            $mapping)->getItems()[0]->getPrice()->shouldBeAnInstanceOf(OrderItemPrice::class);
        $this->deserializeArray($array, $mapping)->getItems()[0]->getPrice()->getValue()->shouldReturn(55);
        $this->deserializeArray($array,
            $mapping)->getItems()[0]->getPrice()->shouldBeAnInstanceOf(OrderItemPrice::class);
        $this->deserializeArray($array, $mapping)->getItems()[0]->getPrice()->getCurrency()->shouldReturn('EUR');

        $this->deserializeArray($array, $mapping)->getItems()[1]->shouldBeAnInstanceOf(OrderItem::class);
        $this->deserializeArray($array,
            $mapping)->getItems()[1]->getPrice()->shouldBeAnInstanceOf(OrderItemPrice::class);
        $this->deserializeArray($array, $mapping)->getItems()[1]->getPrice()->getValue()->shouldReturn(22);
        $this->deserializeArray($array,
            $mapping)->getItems()[1]->getPrice()->shouldBeAnInstanceOf(OrderItemPrice::class);
        $this->deserializeArray($array, $mapping)->getItems()[1]->getPrice()->getCurrency()->shouldReturn('PLN');
    }

    function it_can_deserialize_from_array_with_mapping(Mapping $mapping)
    {
        $array = [
            'nestedObject' => [
                'subField' => 'deserialized subField',
                'subFieldArray' => ['d', 'e', 'f'],
            ],
        ];
        $mapping->isObject()->willReturn(true);
        $mapping->getType()->willReturn(TestObject::class);
        $mapping->has('nestedObject')->willReturn(true);
        $mapping->has(Argument::any())->willReturn(false);
        $mapping->getType('nestedObject')->willReturn(NestedObject::class);
        $mapping->isArrayOfObjects('nestedObject')->willReturn(false);
        $mapping->isObject('nestedObject')->willReturn(true);

        $this->deserializeArray($array, $mapping)
            ->getNestedObject()->shouldBeAnInstanceOf(NestedObject::class);
        $this->deserializeArray($array, $mapping)
            ->getNestedObject()->getSubField()->shouldBe('deserialized subField');
        $this->deserializeArray($array, $mapping)
            ->getNestedObject()->getSubFieldArray()->shouldBe(['d', 'e', 'f']);
    }

    function it_can_serialize_to_json()
    {
        $object = new TestObject();
        $this->serializeToJson($object)->shouldBeString();
        $this->serializeToJson($object)->shouldBe('{"publicField":"public","protectedField":"protected","privateField":"private","nestedObject":{"subField":"sub field","subFieldArray":["a","b","c"]}}');
    }

    function it_can_deserialize_from_json(Mapping $mapping)
    {
        $json = '{"publicField":"deserialized public","protectedField":"deserialized protected","privateField":"deserialized private","nestedObject":{"subField":"deserialized sub field","subFieldArray":["d","e","f"]}}';

        $mapping->isObject()->willReturn(true);
        $mapping->getType()->willReturn(TestObject::class);
        $mapping->has(Argument::any())->willReturn(false);

        $this->deserializeJson($json, $mapping)->shouldBeAnInstanceOf(TestObject::class);
        $this->deserializeJson($json, $mapping)->getPublicField()->shouldBe('deserialized public');
        $this->deserializeJson($json, $mapping)->getProtectedField()->shouldBe('deserialized protected');
        $this->deserializeJson($json, $mapping)->getPrivateField()->shouldBe('deserialized private');
        $this->deserializeJson($json, $mapping)
            ->getNestedObject()->shouldBeArray();
        $this->deserializeJson($json, $mapping)
            ->getNestedObject()['subField']->shouldBe('deserialized sub field');
        $this->deserializeJson($json, $mapping)
            ->getNestedObject()['subFieldArray']->shouldBe(['d', 'e', 'f']);
    }

    function it_can_deserialize_from_json_with_mapping(Mapping $mapping)
    {
        $json = '{"publicField":"deserialized public","protectedField":"deserialized protected","privateField":"deserialized private","nestedObject":{"subField":"deserialized sub field","subFieldArray":["d","e","f"]}}';
        $mapping->has('nestedObject')->willReturn(true);
        $mapping->getType('nestedObject')->willReturn(NestedObject::class);
        $mapping->has(Argument::any())->willReturn(false);

        $mapping->isObject()->willReturn(true);
        $mapping->getType()->willReturn(TestObject::class);
        $mapping->isArrayOfObjects('nestedObject')->willReturn(false);
        $mapping->isObject('nestedObject')->willReturn(true);

        $this->deserializeJson($json, $mapping)
            ->getNestedObject()->shouldBeAnInstanceOf(NestedObject::class);
        $this->deserializeJson($json, $mapping)
            ->getNestedObject()->getSubField()->shouldBe('deserialized sub field');
        $this->deserializeJson($json, $mapping)
            ->getNestedObject()->getSubFieldArray()->shouldBe(['d', 'e', 'f']);
    }

    function it_throws_exception_on_unknown_format()
    {
        $this->shouldThrow(FormatNotFoundException::class)->duringDeserialize([], 'exe');
        $this->shouldThrow(FormatNotFoundException::class)->duringSerialize([], 'exe');
    }
}
