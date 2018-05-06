<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Filter;

use xiio\ObjectSerializer\Exception\InvalidCallbackException;

class CallbackFilter implements FilterInterface
{
    private $callback;

    /**
     * @param $callback
     *
     * @throws \xiio\ObjectSerializer\Exception\InvalidCallbackException
     */
    public function __construct($callback)
    {
        $this->assertCallback($callback);
        $this->callback = $callback;
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function isExcluded(string $fieldName): bool
    {
        return call_user_func($this->callback, $fieldName);
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
}
