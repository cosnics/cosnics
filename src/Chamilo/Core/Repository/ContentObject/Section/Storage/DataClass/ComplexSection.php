<?php
namespace Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

class ComplexSection extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Section::CONTEXT;
}
