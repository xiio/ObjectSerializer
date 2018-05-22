<?php

namespace spec\xiio\ObjectSerializer\Mapping;

use PhpSpec\ObjectBehavior;
use spec\xiio\ObjectSerializer\Fixtures\Address;
use spec\xiio\ObjectSerializer\Fixtures\Order;
use spec\xiio\ObjectSerializer\Fixtures\OrderItem;
use spec\xiio\ObjectSerializer\Fixtures\OrderItemPrice;
use xiio\ObjectSerializer\Exception\ClassNotFoundException;
use xiio\ObjectSerializer\Exception\MappingNotFoundException;
use xiio\ObjectSerializer\Mapping\Type\ArrayType;

class MappingSpec extends ObjectBehavior
{

    function it_can_map_fields_to_classes(ArrayType $arrayType)
    {
        $this->beConstructedWith($arrayType);
        $this->mapClass('std_class', \stdClass::class);
        $this->mapClass('address', Address::class);
        $this->shouldThrow(ClassNotFoundException::class)->duringMapClass('address', \NonExistentClass::class);

        $this->has('std_class')->shouldReturn(true);
        $this->has('address')->shouldReturn(true);
        $this->has('test_field')->shouldReturn(false);

        $this->getType('std_class')->shouldReturn(\stdClass::class);
        $this->getType('address')->shouldReturn(Address::class);
        $this->shouldThrow(MappingNotFoundException::class)->duringGetType('test_field');
    }

    function it_can_map_nested_fields_to_classes(ArrayType $arrayType)
    {
        $this->beConstructedWith($arrayType);
        $this->mapClass('order', Order::class);
        $this->mapArray('order.items', OrderItem::class);
        $this->mapClass('order.items.price', OrderItemPrice::class);

        $this->has('order')->shouldReturn(true);
        $this->has('order.items')->shouldReturn(true);
        $this->has('order.items.price')->shouldReturn(true);
        $this->has('items')->shouldReturn(false);
        $this->has('items.price')->shouldReturn(false);
        $this->has('price')->shouldReturn(false);

         $this->getType('order')->shouldReturn(Order::class);
         $this->getType('order.items')->shouldReturn(OrderItem::class);
         $this->getType('order.items.price')->shouldReturn(OrderItemPrice::class);
    }
}
