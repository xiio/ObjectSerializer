<?php

namespace spec\xiio\ObjectSerializer\Filter;

use PhpSpec\ObjectBehavior;
use spec\xiio\ObjectSerializer\Fixtures\TestObject;
use xiio\ObjectSerializer\Filter\PropertyFilter;

class PropertyFilterSpec extends ObjectBehavior
{

    function it_should_know_supperted_object(TestObject $testObject, \stdClass $notSupportedObject)
    {
        $this->beConstructedWith(TestObject::class, PropertyFilter::STRATEGY_BLACKLIST);
        $this->supports($testObject)->shouldReturn(true);
        $this->supports($notSupportedObject)->shouldReturn(false);
    }

    function it_can_remove_specific_fields()
    {
        $this->beConstructedWith(TestObject::class, PropertyFilter::STRATEGY_BLACKLIST);
        $testPayload = [
            'removeThisField' => '',
            'leaveThisField' => '',
            'leaveThisFieldToo' => '',
        ];

        $this->addField('removeThisField');
        $this->filter($testPayload)->shouldHaveCount(2);
        $this->filter($testPayload)->shouldNotHaveKey('removeThisField');
        $this->filter($testPayload)->shouldHaveKey('leaveThisField');
        $this->filter($testPayload)->shouldHaveKey('leaveThisFieldToo');
    }

    function it_can_leave_specific_fields()
    {
        $this->beConstructedWith(TestObject::class, PropertyFilter::STRATEGY_WHITELIST);
        $testPayload = [
            'leaveThisField' => '',
            'removeThisField' => '',
            'removeThisFieldToo' => '',
        ];

        $this->addField('leaveThisField');
        $this->filter($testPayload)->shouldHaveCount(1);
        $this->filter($testPayload)->shouldHaveKey('leaveThisField');
        $this->filter($testPayload)->shouldNotHaveKey('removeThisField');
        $this->filter($testPayload)->shouldNotHaveKey('removeThisFieldToo');
    }

    function it_should_remove_all_fields_when_none_specified_on_whitelist_mode()
    {
        $this->beConstructedWith(TestObject::class, PropertyFilter::STRATEGY_WHITELIST);
        $testPayload = [
            'leaveThisField' => '',
            'removeThisField' => '',
            'removeThisFieldToo' => '',
        ];

        $this->filter($testPayload)->shouldHaveCount(0);
    }

    function it_should_leave_all_fields_when_none_specified_on_blacklist_mode()
    {
        $this->beConstructedWith(TestObject::class, PropertyFilter::STRATEGY_BLACKLIST);
        $testPayload = [
            'leaveThisField' => '',
            'removeThisField' => '',
            'removeThisFieldToo' => '',
        ];

        $this->filter($testPayload)->shouldHaveCount(3);
    }
}
