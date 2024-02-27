<?php
namespace Chamilo\Core\Repository\ContentObject\File\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\File\Storage\DataClass
 */
class ComplexFile extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = File::CONTEXT;
}
