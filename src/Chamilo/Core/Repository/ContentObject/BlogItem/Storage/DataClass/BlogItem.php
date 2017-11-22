<?php
namespace Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.lib.content_object.blog_item
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * This class represents an blog_item
 */
class BlogItem extends ContentObject implements Versionable, AttachmentSupport
{

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }
}
