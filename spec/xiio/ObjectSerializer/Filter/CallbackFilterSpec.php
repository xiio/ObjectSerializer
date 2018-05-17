<?php

namespace spec\xiio\ObjectSerializer\Filter;

use PhpSpec\ObjectBehavior;
use spec\xiio\ObjectSerializer\Fixtures\TestObject;

class CallbackFilterSpec extends ObjectBehavior
{
    function it_can_filter_fields_using_callable()
    {
        $filterCallback = function (array $objectData) {
            unset($objectData['removeThisField']);
            $objectData['changeThisField'] = 'changed';

            return $objectData;
        };
        $testPayload = [
            'leaveThisField' => '',
            'removeThisField' => '',
            'changeThisField' => '',
        ];
        $this->beConstructedWith(TestObject::class, $filterCallback);

        $this->filter($testPayload)->shouldHaveCount(2);
        $this->filter($testPayload)->shouldHaveKeyWithValue('leaveThisField', '');
        $this->filter($testPayload)->shouldHaveKeyWithValue('changeThisField', 'changed');
    }
}
