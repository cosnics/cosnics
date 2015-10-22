<?php
namespace Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * $Id: introduction.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.content_object.introduction
 */
/**
 * An Introduction
 */
class Introduction extends ContentObject implements Versionable
{

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
        ;
    }
}
