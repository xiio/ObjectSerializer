<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Serialization;

use xiio\ObjectSerializer\Exception\InvalidJsonException;
use xiio\ObjectSerializer\Filter\FilterInterface;
use xiio\ObjectSerializer\Hydration\Hydrator;
use xiio\ObjectSerializer\Mapping\Mapping;

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
     * @param $jsonData
     * @param \xiio\ObjectSerializer\Mapping\Mapping|null $mapping
     *
     * @return array|mixed|object
     * @throws \xiio\ObjectSerializer\Exception\InvalidJsonException
     * @throws \xiio\ObjectSerializer\Exception\MappingNotFoundException
     */
    public function deserialize($jsonData, Mapping $mapping = null)
    {
        $this->assertJson($jsonData);

        return Deserializer::deserialize(json_decode($jsonData, true), $mapping);
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
