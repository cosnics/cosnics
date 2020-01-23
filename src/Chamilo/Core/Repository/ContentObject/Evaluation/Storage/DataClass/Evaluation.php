<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.lib.content_object.evaluation
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * A Evaluation
 */
class Evaluation extends ContentObject implements Versionable
{

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }
}
