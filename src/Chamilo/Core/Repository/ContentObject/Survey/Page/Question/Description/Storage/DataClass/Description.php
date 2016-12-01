<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.content_object.survey_description
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class Description extends ContentObject implements Versionable
{

    static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }
}

?>