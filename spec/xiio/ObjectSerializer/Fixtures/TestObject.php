<?php

namespace spec\xiio\ObjectSerializer\Fixtures;

class TestObject
{
    public $publicField = 'public';
    protected $protectedField = 'protected';
    private $privateField = 'private';
    private $nestedObject;

    /**
     * TestObject constructor.
     */
    public function __construct()
    {
        $this->nestedObject = new NestedObject();
    }

    /**
     * @return string
     */
    public function getPublicField(): string
    {
        return $this->publicField;
    }

    /**
     * @return string
     */
    public function getProtectedField(): string
    {
        return $this->protectedField;
    }

    /**
     * @return string
     */
    public function getPrivateField(): string
    {
        return $this->privateField;
    }

    public function getNestedObject()
    {
        return $this->nestedObject;
    }
}
