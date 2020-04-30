<?php
namespace Chamilo\Core\Repository\ContentObject\Indicator\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * This class represents an indicator
 */
class Indicator extends ContentObject
{

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }
}
