<?php
namespace Chamilo\Core\Repository\ContentObject\File\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 *
 * @package repository.lib.content_object.document
 */
class ComplexFile extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = File::CONTEXT;
}
