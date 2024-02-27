<?php
namespace Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass
 */
class ComplexPage extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Page::CONTEXT;
}
