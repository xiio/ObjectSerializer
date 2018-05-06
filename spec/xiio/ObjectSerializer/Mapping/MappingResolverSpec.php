<?php

namespace spec\xiio\ObjectSerializer\Mapping;

use PhpSpec\ObjectBehavior;
use spec\xiio\ObjectSerializer\Fixtures\TestObject;
use xiio\ObjectSerializer\Mapping\Type\ArrayOfObjectProperty;
use xiio\ObjectSerializer\Mapping\Type\ObjectProperty;

class MappingResolverSpec extends ObjectBehavior
{
    function it_can_resolve_mapping_for_object(ObjectProperty $type)
    {
        $type->getTypeName()->willReturn('class');
        $type->getType()->willReturn(TestObject::class);

        $payload = [
            'privateField' => 'test sub field',
        ];

        self::resolveType($type, $payload)->shouldBeAnInstanceOf(TestObject::class);
        self::resolveType($type, $payload)->getPrivateField()->shouldBe('test sub field');
        self::resolveType($type, $payload)->getProtectedField()->shouldBe('protected');
        self::resolveType($type, $payload)->getPublicField()->shouldBe('public');
        self::resolveType($type, $payload)->getNestedObject()->shouldBe(null);
    }

    function it_can_resolve_mapping_for_array_of_object(ArrayOfObjectProperty $type)
    {
        $type->getTypeName()->willReturn('class_array');
        $type->getType()->willReturn(TestObject::class);

        $payload = [
            ['privateField' => 'test sub field 0',],
            ['privateField' => 'test sub field 1',],
            ['privateField' => 'test sub field 2',],
        ];

        self::resolveType($type, $payload)->shouldBeArray();
        self::resolveType($type, $payload)->shouldHaveCount(3);
        self::resolveType($type, $payload)[0]->shouldBeAnInstanceOf(TestObject::class);
        self::resolveType($type, $payload)[1]->shouldBeAnInstanceOf(TestObject::class);
        self::resolveType($type, $payload)[2]->shouldBeAnInstanceOf(TestObject::class);

        self::resolveType($type, $payload)[0]->getPrivateField()->shouldBe('test sub field 0');
        self::resolveType($type, $payload)[0]->getProtectedField()->shouldBe('protected');
        self::resolveType($type, $payload)[0]->getPublicField()->shouldBe('public');
        self::resolveType($type, $payload)[1]->getPrivateField()->shouldBe('test sub field 1');
        self::resolveType($type, $payload)[1]->getProtectedField()->shouldBe('protected');
        self::resolveType($type, $payload)[1]->getPublicField()->shouldBe('public');
        self::resolveType($type, $payload)[2]->getPrivateField()->shouldBe('test sub field 2');
        self::resolveType($type, $payload)[2]->getProtectedField()->shouldBe('protected');
        self::resolveType($type, $payload)[2]->getPublicField()->shouldBe('public');
    }
}
