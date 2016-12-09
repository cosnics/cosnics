<?php
namespace Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * $Id: note.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.note
 */
/**
 * This class represents an note
 */
class Note extends ContentObject implements Versionable, AttachmentSupport
{

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
        ;
    }
}
