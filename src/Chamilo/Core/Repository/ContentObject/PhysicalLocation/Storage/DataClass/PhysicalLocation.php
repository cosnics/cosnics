<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass
 */
class PhysicalLocation extends ContentObject implements VersionableInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\PhysicalLocation';

    public const PROPERTY_LOCATION = 'location';

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_LOCATION];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_physical_location';
    }

    public function get_location()
    {
        return $this->getAdditionalProperty(self::PROPERTY_LOCATION);
    }

    public static function get_searchable_property_names()
    {
        return [self::PROPERTY_LOCATION];
    }

    public function set_location($location)
    {
        return $this->setAdditionalProperty(self::PROPERTY_LOCATION, $location);
    }
}
