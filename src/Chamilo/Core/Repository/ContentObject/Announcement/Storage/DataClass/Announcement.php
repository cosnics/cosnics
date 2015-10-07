<?php
namespace Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * $Id: announcement.class.php 200 2009-11-13
 * 
 * @package repository.lib.content_object.announcement
 */
/**
 * This class represents an announcement
 */
class Announcement extends ContentObject implements Versionable, AttachmentSupport
{
    const CLASS_NAME = __CLASS__;

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME, true);
    }
}
