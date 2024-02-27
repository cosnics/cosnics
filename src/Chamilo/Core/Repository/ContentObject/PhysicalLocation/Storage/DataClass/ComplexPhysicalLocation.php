<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass
 */
class ComplexPhysicalLocation extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = PhysicalLocation::CONTEXT;
}
