<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Storage\DataClass\NestedSet;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 * @author  Sven Vanpoucke
 */
class Group extends NestedSet
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CODE = 'code';
    public const PROPERTY_DESCRIPTION = 'description';
    public const PROPERTY_NAME = 'name';

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_NAME,
                self::PROPERTY_DESCRIPTION,
                self::PROPERTY_CODE
            ]
        );
    }

    public static function getStorageUnitName(): string
    {
        return 'group_group';
    }

    public function get_code(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_CODE);
    }

    public function get_description(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    public function get_name(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    public function set_code(?string $code): void
    {
        $this->setDefaultProperty(self::PROPERTY_CODE, $code);
    }

    public function set_description(?string $description): void
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    public function set_name(?string $name): void
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }
}
