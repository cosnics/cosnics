<?php
namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.lib.content_object.gradebook
 */
/**
 * This class represents a gradebook
 */
class GradeBook extends ContentObject implements Versionable
{
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        $propertyNames = parent::get_additional_property_names();
        return $propertyNames;
    }
}