<?php
namespace Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.description
 * @author  Hans De Bisschop
 * @author  Dieter De Neef
 */
class ComplexDescription extends ComplexContentObjectItem implements CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = Description::CONTEXT;
}
