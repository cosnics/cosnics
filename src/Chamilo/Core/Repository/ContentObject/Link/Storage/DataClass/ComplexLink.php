<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.link
 */
class ComplexLink extends ComplexContentObjectItem implements CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = Link::CONTEXT;
}
