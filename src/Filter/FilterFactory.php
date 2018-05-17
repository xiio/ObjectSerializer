<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Filter;

class FilterFactory
{
    /**
     * @param string $supportedClass
     *
     * @return \xiio\ObjectSerializer\Filter\PropertyFilter
     */
    public static function whitelist(string $supportedClass): PropertyFilter
    {
        return new PropertyFilter($supportedClass, PropertyFilter::STRATEGY_WHITELIST);
    }

    /**
     * @param string $supportedClass
     *
     * @return \xiio\ObjectSerializer\Filter\PropertyFilter
     */
    public static function blacklist(string $supportedClass): PropertyFilter
    {
        return new PropertyFilter($supportedClass, PropertyFilter::STRATEGY_BLACKLIST);
    }

    /**
     * @param string $supportedClass
     * @param $callback
     *
     * @return \xiio\ObjectSerializer\Filter\CallbackFilter
     */
    public static function callback(string $supportedClass, $callback): CallbackFilter
    {
        return new CallbackFilter($supportedClass, $callback);
    }
}
