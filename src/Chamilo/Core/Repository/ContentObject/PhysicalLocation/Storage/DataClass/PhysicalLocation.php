<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * $Id: physical_location.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.content_object.physical_location
 */
/**
 * This class represents an physical_location
 */
class PhysicalLocation extends ContentObject implements Versionable
{
    const PROPERTY_LOCATION = 'location';

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
        ;
    }

    public function get_location()
    {
        return $this->get_additional_property(self :: PROPERTY_LOCATION);
    }

    public function set_location($location)
    {
        return $this->set_additional_property(self :: PROPERTY_LOCATION, $location);
    }

    public static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOCATION);
    }

    public static function get_searchable_property_names()
    {
        return array(self :: PROPERTY_LOCATION);
    }
}
