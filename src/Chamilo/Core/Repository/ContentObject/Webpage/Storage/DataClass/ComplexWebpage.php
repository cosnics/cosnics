<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass
 */
class ComplexWebpage extends ComplexContentObjectItem implements CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = Webpage::CONTEXT;
}
