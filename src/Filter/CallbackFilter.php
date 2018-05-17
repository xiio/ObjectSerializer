<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Filter;

use xiio\ObjectSerializer\Exception\InvalidCallbackException;

/**
 * @package xiio\ObjectSerializer\Filter
 */
class CallbackFilter extends Filter
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @var string
     */
    private $supportedClass;

    /**
     * CallbackFilter constructor.
     *
     * @param string $supportedClass
     * @param $callback
     *
     * @throws \xiio\ObjectSerializer\Exception\ClassNotFoundException
     * @throws \xiio\ObjectSerializer\Exception\InvalidCallbackException
     */
    public function __construct(string $supportedClass, $callback)
    {
        $this->assertClassExists($supportedClass);
        $this->assertCallback($callback);

        $this->supportedClass = $supportedClass;
        $this->callback = $callback;
    }

    /**
     * @param $objectData
     *
     * @return array filtered object data
     */
    public function filter(array $objectData): array
    {
        return call_user_func($this->callback, $objectData);
    }

    /**
     * @param $callback
     *
     * @throws \xiio\ObjectSerializer\Exception\InvalidCallbackException
     */
    private function assertCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException("$callback have to be callable.");
        }
    }

    /**
     * get supported class name
     * @return string
     */
    function supportedClass(): string
    {
        return $this->supportedClass;
    }
}
