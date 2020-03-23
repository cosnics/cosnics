<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;

/**
 *
 * @package repository.lib.content_object.glossary
 */
/**
 * This class represents an glossary
 */
class Glossary extends ContentObject implements ComplexContentObjectSupport
{

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public function get_allowed_types()
    {
        return array(GlossaryItem::class_name());
    }
}
