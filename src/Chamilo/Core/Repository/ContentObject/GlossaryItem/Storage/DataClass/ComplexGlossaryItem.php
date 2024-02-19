<?php
namespace Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.glossary_item
 */
class ComplexGlossaryItem extends ComplexContentObjectItem implements CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = GlossaryItem::CONTEXT;
}
