<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Filter;

use xiio\ObjectSerializer\Exception\ClassNotFoundException;

abstract class Filter implements FilterInterface
{

    /**
     * get supported class name
     * @return string
     */
    abstract function supportedClass(): string;

    /**
     * @param $object
     *
     * @return bool
     */
    public function supports($object): bool
    {
        $supportedClass = $this->supportedClass();

        return $object instanceof $supportedClass;
    }

    /**
     * @param string $className
     *
     * @throws \xiio\ObjectSerializer\Exception\ClassNotFoundException
     */
    protected function assertClassExists(string $className)
    {
        if (!class_exists($className)) {
            throw new ClassNotFoundException(sprintf("Class %s not found.", $className));
        }
    }
}
