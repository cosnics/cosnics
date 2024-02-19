<?php
namespace Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass
 */
class Introduction extends ContentObject implements VersionableInterface, CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Introduction';
}
