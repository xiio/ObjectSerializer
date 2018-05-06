<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Mapping\Type;

use xiio\ObjectSerializer\Exception\ClassNotFoundException;

class ObjectProperty implements MappingType
{

    const TYPE = 'class';

    /**
     * @var string
     */
    private $class;

    /**
     * @param $class
     *
     * @throws \xiio\ObjectSerializer\Exception\ClassNotFoundException
     */
    public function __construct(string $class)
    {
        $this->assertClass($class);
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getType(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return static::TYPE;
    }

    /**
     * @param string $class
     *
     * @throws \xiio\ObjectSerializer\Exception\ClassNotFoundException
     */
    private function assertClass(string $class)
    {
        if (!class_exists($class)) {
            throw new ClassNotFoundException(sprintf("Class %s doesn't exists.", $class));
        }
    }
}
