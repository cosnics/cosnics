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

    public static function getTypeName(): string
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    public function get_allowed_types(): array
    {
        return array(GlossaryItem::class);
    }
}
