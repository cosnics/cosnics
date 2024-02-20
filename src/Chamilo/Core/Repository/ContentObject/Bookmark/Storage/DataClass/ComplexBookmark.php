<?php
namespace Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

class ComplexBookmark extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Bookmark::CONTEXT;
}
