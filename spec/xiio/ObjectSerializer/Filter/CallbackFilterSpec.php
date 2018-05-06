<?php

namespace spec\xiio\ObjectSerializer\Filter;

use PhpSpec\ObjectBehavior;
use xiio\ObjectSerializer\Exception\InvalidCallbackException;

class CallbackFilterSpec extends ObjectBehavior
{

    function it_can_filter_using_callback()
    {
        $callback = function ($fieldname) {
            return $fieldname === 'test_field';
        };

        $this->beConstructedWith($callback);

        $this->isExcluded('test_field')->shouldReturn(true);
        $this->isExcluded('test_field_1')->shouldReturn(false);
    }

    function it_should_throw_error_on_invalid_callback()
    {
        $this->beConstructedWith(null);
        $this->shouldThrow(InvalidCallbackException::class)->duringInstantiation();

        $this->beConstructedWith(true);
        $this->shouldThrow(InvalidCallbackException::class)->duringInstantiation();
    }
}
