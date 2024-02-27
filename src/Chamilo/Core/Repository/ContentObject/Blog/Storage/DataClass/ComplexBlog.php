<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass
 */
class ComplexBlog extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Blog::CONTEXT;
}
