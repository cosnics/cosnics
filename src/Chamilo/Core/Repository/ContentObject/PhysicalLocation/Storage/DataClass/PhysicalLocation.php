<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.lib.content_object.physical_location
 */
/**
 * This class represents an physical_location
 */
class PhysicalLocation extends ContentObject implements Versionable
{
    const PROPERTY_LOCATION = 'location';

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_physical_location';
    }

    public static function getTypeName(): string
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    public function get_location()
    {
        return $this->getAdditionalProperty(self::PROPERTY_LOCATION);
    }

    public function set_location($location)
    {
        return $this->setAdditionalProperty(self::PROPERTY_LOCATION, $location);
    }

    public static function getAdditionalPropertyNames(): array
    {
        return array(self::PROPERTY_LOCATION);
    }

    public static function get_searchable_property_names()
    {
        return array(self::PROPERTY_LOCATION);
    }
}
