<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use stdClass;

/**
 * @package Chamilo\Configuration\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Package extends DataClass
{
    public const CONTEXT = 'Chamilo\Configuration\Package';

    public const PROPERTY_COMPOSER_JSON_OBJECT = 'extra';
    public const PROPERTY_CONTEXT = 'context';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_RESOURCES = 'resources';
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_VERSION = 'version';

    public const TYPE_APPLICATION = 'Chamilo\Application';
    public const TYPE_CORE = 'Chamilo\Core';

    public function getComposerJsonObject(): stdClass
    {
        return unserialize($this->getDefaultProperty(self::PROPERTY_COMPOSER_JSON_OBJECT));
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return mixed
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTEXT;
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_VERSION;
        $extendedPropertyNames[] = self::PROPERTY_COMPOSER_JSON_OBJECT;
        $extendedPropertyNames[] = self::PROPERTY_RESOURCES;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return stdClass[]
     */
    public function getResources(): array
    {
        return unserialize($this->getDefaultProperty(self::PROPERTY_RESOURCES));
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'configuration_package';
    }

    public function getType(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    public function get_context(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    public function get_name(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    public function get_version(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_VERSION);
    }

    public function isApplication(): bool
    {
        return in_array($this->getType(), [self::TYPE_APPLICATION, self::TYPE_CORE]);
    }

    public function setComposerJsonObject(stdClass $composerJsonObject): Package
    {
        $this->setDefaultProperty(self::PROPERTY_COMPOSER_JSON_OBJECT, serialize($composerJsonObject));

        return $this;
    }

    /**
     * @param string[][] $resources
     */
    public function setResources(array $resources): Package
    {
        $this->setDefaultProperty(self::PROPERTY_RESOURCES, serialize($resources));

        return $this;
    }

    public function setType(string $type): Package
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);

        return $this;
    }

    public function set_context(string $context): Package
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);

        return $this;
    }

    public function set_name(string $name): Package
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);

        return $this;
    }

    public function set_version(string $version): Package
    {
        $this->setDefaultProperty(self::PROPERTY_VERSION, $version);

        return $this;
    }
}
