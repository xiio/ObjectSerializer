<?php

namespace spec\xiio\ObjectSerializer\Filter;

use PhpSpec\ObjectBehavior;
use xiio\ObjectSerializer\Filter\PropertyFilter;

class PropertyFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PropertyFilter::class);
    }

    function it_can_set_fields_to_filter()
    {
        $this->setField('test_field');
        $this->setField('test_field_1');
        $this->setField('test_field_2');

        $this->isExcluded('test_field')->shouldReturn(true);
        $this->isExcluded('test_field_1')->shouldReturn(true);
        $this->isExcluded('test_field_2')->shouldReturn(true);
        $this->isExcluded('test_field_3')->shouldReturn(false);
    }

    function it_can_disable_filter_for_field()
    {
        $this->setField('test_field');
        $this->setField('test_field_1');
        $this->setField('test_field_2');

        $this->setField('test_field_2', false);
        $this->isExcluded('test_field')->shouldReturn(true);
        $this->isExcluded('test_field_1')->shouldReturn(true);
        $this->isExcluded('test_field_2')->shouldReturn(false);
        $this->isExcluded('test_field_3')->shouldReturn(false);
    }
}
