<?php declare(strict_types=1);


namespace xiio\ObjectSerializer\Hydration;

use Doctrine\Instantiator\Instantiator;
use Psr\Log\InvalidArgumentException;
use xiio\ObjectSerializer\Filter\FilterInterface;
use xiio\ObjectSerializer\Mapping\MappingResolver;
use xiio\ObjectSerializer\Mapping\PropertyMapper;

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

    /**
     * Fill object with data
     *
     * @param array $data
     * @param $object
     * @param \xiio\ObjectSerializer\Mapping\PropertyMapper $mapping
     *
     * @return mixed
     */
    public static function populate(array $data, $object, PropertyMapper $mapping = null)
    {
        $thief = function ($object) use ($data, $mapping) {
            foreach ($data as $fieldName => $value) {
                if (!property_exists($object, $fieldName)) {
                    continue;
                }
                if ($mapping && $mapping->has($fieldName) && $value !== null) {
                    $object->{$fieldName} = MappingResolver::resolveType($mapping->get($fieldName), $value);
                } else {
                    $object->{$fieldName} = $value;
                }
            }
        };
        \Closure::bind($thief, null, $object)($object);

        return $object;
    }

    /**
     * Creates an instance of class and populate data to it
     *
     * @param array $data
     * @param string $class
     * @param null $mapping
     *
     * @return object Instance of given class
     */
    public static function deserialize(array $data, string $class, $mapping = null)
    {
        $instantiator = new Instantiator();
        $resultObject = $instantiator->instantiate($class);

        return static::populate($data, $resultObject, $mapping);
    }
}
