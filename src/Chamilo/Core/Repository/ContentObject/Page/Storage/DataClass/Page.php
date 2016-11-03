<?php
namespace Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * This class represents a page
 *
 * @package repository.content_object.page
 */
class Page extends ContentObject implements Versionable, Includeable
{

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
    }
}
