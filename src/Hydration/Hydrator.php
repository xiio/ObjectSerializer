<?php declare(strict_types=1);


namespace xiio\ObjectSerializer\Hydration;

use Psr\Log\InvalidArgumentException;
use xiio\ObjectSerializer\Filter\FilterInterface;

class Hydrator
{

    /**
     * Extract data from object
     *
     * @param $object
     * @param \xiio\ObjectSerializer\Filter\FilterInterface|null $filter
     *
     * @return array
     */
    public static function extract($object, FilterInterface $filter = null): array
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Argument $object is not an valid object.');
        }
        $thief = function ($object) use ($filter) {
            $objectVars = get_object_vars($object);
            $result = [];
            foreach ($objectVars as $fieldName => $value) {
                $result[$fieldName] = Hydrator::extractComplex($value);
            }
            if ($filter && $filter->supports($object)) {
                $result = $filter->filter($result);
            }

            return $result;
        };
        $sweetsThief = \Closure::bind($thief, null, $object);

        return $sweetsThief($object);
    }

    /**
     * Fill object with data
     *
     * @param array $data
     * @param $object
     *
     * @return mixed
     */
    public static function populate(array $data, $object)
    {
        $thief = function ($object, $data) {
            $objectVars = get_class_vars(get_class($object));
            foreach ($objectVars as $objectVarName => $varValue) {
                if (!array_key_exists($objectVarName, $data)) {
                    continue;
                }
                $object->{$objectVarName} = $data[$objectVarName];
            }
        };
        \Closure::bind($thief, null, $object)($object, $data);

        return $object;
    }

    /**
     * It extracts data according to input type. Ex. object, array of objects.
     *
     * @param $value
     *
     * @return array
     */
    public static function extractComplex($value)
    {
        if (is_array($value)) {
            return array_map(function ($item) {
                return is_object($item) ? Hydrator::extract($item) : $item;
            }, $value);
        } else {
            return is_object($value) ? Hydrator::extract($value) : $value;
        }
    }
}
