<?php declare(strict_types=1);

namespace xiio\ObjectSerializer\Filter;

class PropertyFilter extends Filter
{
    /**
     * Allow fields to be serialized/deserialized
     */
    const STRATEGY_WHITELIST = 'whitelist';

    /**
     * Disable fields to be deserialized
     */
    const STRATEGY_BLACKLIST = 'blacklist';

    /**s
     * @var array
     */
    private $fields = [];

    /**
     * @var string
     */
    private $supportedClass;

    /**
     * @var string Strategy used to filetring
     */
    private $strategy;

    /**
     * PropertyFilter constructor.
     *
     * @param string $supportedClass
     * @param string $strategy
     *
     * @throws \xiio\ObjectSerializer\Exception\ClassNotFoundException
     */
    public function __construct(string $supportedClass, $strategy = self::STRATEGY_BLACKLIST)
    {
        $this->assertClassExists($supportedClass);
        $this->supportedClass = $supportedClass;
        $this->strategy = $strategy;
    }

    /**
     * @param string $fieldName
     */
    public function addField(string $fieldName): void
    {
        $this->fields[$fieldName] = true;
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function hasField(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->fields);
    }

    /**
     * @param array $objectData
     *
     * @return array
     */
    public function filter(array $objectData): array
    {
        if ($this->isBlacklist()) {
            return $this->filterAsBlacklist($objectData);
        }

        return $this->filterAsWhitelist($objectData);
    }

    /**
     * @param array $objectData
     *
     * @return array
     */
    private function filterAsBlacklist(array $objectData): array
    {
        foreach ($this->fields as $fieldName => $remove) {
            if ($remove && array_key_exists($fieldName, $objectData)) {
                unset($objectData[$fieldName]);
            }
        }

        return $objectData;
    }

    /**
     * @param array $objectData
     *
     * @return array
     */
    private function filterAsWhitelist(array $objectData): array
    {
        foreach ($objectData as $fieldName => $value) {
            if (!$this->hasField($fieldName)) {
                unset($objectData[$fieldName]);
            }
        }

        return $objectData;
    }

    /**
     * get supported class name
     * @return string
     */
    function supportedClass(): string
    {
        return $this->supportedClass;
    }

    /**
     * @return bool
     */
    public function isBlacklist(): bool
    {
        return $this->strategy === self::STRATEGY_BLACKLIST;
    }
}
