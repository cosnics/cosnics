<?php
namespace Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass
 */
class ComplexGlossaryItem extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = GlossaryItem::CONTEXT;
}
