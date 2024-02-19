<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.physical_location
 */
class ComplexPhysicalLocation extends ComplexContentObjectItem implements CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = PhysicalLocation::CONTEXT;
}
