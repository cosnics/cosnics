<?php
namespace Chamilo\Core\Admin\Storage\DataClass;

use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use stdClass;

/**
 * @package Chamilo\Core\Admin\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Package extends DataClass
{
    public const string CONTEXT = Manager::CONTEXT;
    public const string PROPERTY_COMPOSER_JSON_OBJECT = 'extra';
    public const string PROPERTY_CONTEXT = 'context';
    public const string PROPERTY_NAME = 'name';
    public const string PROPERTY_RESOURCES = 'resources';
    public const string PROPERTY_TYPE = 'type';
    public const string PROPERTY_VERSION = 'version';
    public const string TYPE_APPLICATION = 'Chamilo\Application';
    public const string TYPE_CORE = 'Chamilo\Core';

    public function getContext(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
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

    public function getName(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
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

    public function getVersion(): string
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

    public function setContext(string $context): Package
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);

        return $this;
    }

    public function setName(string $name): Package
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);

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

    public function setVersion(string $version): Package
    {
        $this->setDefaultProperty(self::PROPERTY_VERSION, $version);

        return $this;
    }
}
