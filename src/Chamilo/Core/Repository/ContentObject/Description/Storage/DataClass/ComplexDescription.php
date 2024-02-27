<?php
namespace Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass
 * @author  Hans De Bisschop
 * @author  Dieter De Neef
 */
class ComplexDescription extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Description::CONTEXT;
}
