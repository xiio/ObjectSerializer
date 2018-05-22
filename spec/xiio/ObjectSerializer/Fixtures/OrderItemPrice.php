<?php


namespace spec\xiio\ObjectSerializer\Fixtures;


class OrderItemPrice
{

    private $price = 22;
    private $currency = 'USD';

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}
