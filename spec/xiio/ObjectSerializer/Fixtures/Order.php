<?php


namespace spec\xiio\ObjectSerializer\Fixtures;


class Order
{
    private $id;
    /**
     * @var
     */
    private $items = [];

    /**
     * Order constructor.
     *
     * @param $id
     * @param array $items
     */
    public function __construct($id, array $items)
    {
        $this->id = $id;
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }
}
