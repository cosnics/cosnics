<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass
 */
class ComplexRssFeed extends ComplexContentObjectItem implements CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = RssFeed::CONTEXT;
}
