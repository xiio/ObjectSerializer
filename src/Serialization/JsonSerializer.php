<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Serialization;

use xiio\ObjectSerializer\Exception\InvalidJsonException;
use xiio\ObjectSerializer\Filter\FilterInterface;
use xiio\ObjectSerializer\Hydration\Hydrator;
use xiio\ObjectSerializer\Mapping\PropertyMapper;

final class JsonSerializer implements SerializerInterface
{
    const FORMAT = 'json';

    /**
     * @param $object
     * @param \xiio\ObjectSerializer\Filter\FilterInterface|null $filter
     *
     * @return string
     */
    public function serialize($object, FilterInterface $filter = null): string
    {
        return json_encode(Hydrator::extract($object, $filter));
    }

    /**
     * @param string $jsonData
     * @param string $class
     * @param \xiio\ObjectSerializer\Mapping\PropertyMapper|null $mapping
     *
     * @return object
     * @throws \xiio\ObjectSerializer\Exception\InvalidJsonException
     */
    public function deserialize($jsonData, string $class, PropertyMapper $mapping = null)
    {
        $this->assertJson($jsonData);

        return Hydrator::deserialize(json_decode($jsonData, true), $class, $mapping);
    }

    public function getFormatName(): string
    {
        return self::FORMAT;
    }

    /**
     * @param string $jsonData
     *
     * @throws \xiio\ObjectSerializer\Exception\InvalidJsonException
     */
    private function assertJson(string $jsonData)
    {
        json_decode($jsonData);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException(json_last_error_msg());
        }
    }
}
