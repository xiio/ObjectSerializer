<?php


namespace spec\xiio\ObjectSerializer\Fixtures;


class OrderItem
{

    private $price;

    /**
     * OrderItem constructor.
     *
     * @param $price
     */
    public function __construct(OrderItemPrice $price)
    {
        $this->price = $price;
    }

    /**
     * @return \spec\xiio\ObjectSerializer\Fixtures\OrderItemPrice
     */
    public function getPrice(): \spec\xiio\ObjectSerializer\Fixtures\OrderItemPrice
    {
        return $this->price;
    }
}
