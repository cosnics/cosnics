<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass
 */
class ComplexLink extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Link::CONTEXT;
}
