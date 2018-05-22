<?php declare(strict_types=1);

namespace xiio\ObjectSerializer;

use xiio\ObjectSerializer\Exception\FormatNotFoundException;
use xiio\ObjectSerializer\Filter\FilterInterface;
use xiio\ObjectSerializer\Mapping\Mapping;
use xiio\ObjectSerializer\Serialization\ArraySerializer;
use xiio\ObjectSerializer\Serialization\JsonSerializer;
use xiio\ObjectSerializer\Serialization\SerializerInterface;

class ObjectSerializer
{

    /**
     * @var SerializerInterface[]
     */
    private $serializers = [];

    public function __construct()
    {
        $this->configure();
    }

    /**
     * @param $object
     * @param null $filter
     *
     * @return string
     * @throws \xiio\ObjectSerializer\Exception\FormatNotFoundException
     */
    public function serializeToJson($object, $filter = null): string
    {
        return $this->serialize($object, JsonSerializer::FORMAT, $filter);
    }

    /**
     * @param $object
     * @param null $filter
     *
     * @return array
     * @throws \xiio\ObjectSerializer\Exception\FormatNotFoundException
     */
    public function serializeToArray($object, $filter = null): array
    {
        return $this->serialize($object, ArraySerializer::FORMAT, $filter);
    }

    /**
     * @param $object
     * @param string $format
     * @param \xiio\ObjectSerializer\Filter\FilterInterface|null $filter
     *
     * @return mixed
     * @throws \xiio\ObjectSerializer\Exception\FormatNotFoundException
     */
    public function serialize($object, string $format, FilterInterface $filter = null)
    {
        if (!array_key_exists($format, $this->serializers)) {
            throw new FormatNotFoundException(sprintf("Serialization format %s is not supported.", $format));
        }

        return $this->serializers[$format]->serialize($object, $filter);
    }

    /**
     * @param string $data
     * @param Mapping $mapping
     *
     * @return object
     * @throws \xiio\ObjectSerializer\Exception\FormatNotFoundException
     */
    public function deserializeJson(string $data, Mapping $mapping = null)
    {
        return $this->deserialize($data, JsonSerializer::FORMAT, $mapping);
    }

    /**
     * @param array $data
     * @param Mapping $mapping
     *
     * @return mixed
     * @throws \xiio\ObjectSerializer\Exception\FormatNotFoundException
     */
    public function deserializeArray(array $data, Mapping $mapping = null)
    {
        return $this->deserialize($data, ArraySerializer::FORMAT, $mapping);
    }

    /**
     * @param $data
     * @param string $format
     * @param Mapping $mapping
     *
     * @return mixed
     * @throws \xiio\ObjectSerializer\Exception\FormatNotFoundException
     */
    public function deserialize($data, string $format, Mapping $mapping = null)
    {
        if (!array_key_exists($format, $this->serializers)) {
            throw new FormatNotFoundException(sprintf("Deserialization format %s is not supported.", $format));
        }

        return $this->serializers[$format]->deserialize($data, $mapping);
    }

    /**
     * @param \xiio\ObjectSerializer\Serialization\SerializerInterface $serializer
     */
    public function registerSerializer(SerializerInterface $serializer): void
    {
        $this->serializers[$serializer->getFormatName()] = $serializer;
    }

    /**
     * Configure with built in serializers
     */
    private function configure()
    {
        $this->registerSerializer(new ArraySerializer());
        $this->registerSerializer(new JsonSerializer());
    }
}
