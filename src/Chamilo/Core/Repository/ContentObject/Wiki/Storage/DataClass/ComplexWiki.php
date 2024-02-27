<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Storage\DataClass
 */
class ComplexWiki extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Wiki::CONTEXT;
}
