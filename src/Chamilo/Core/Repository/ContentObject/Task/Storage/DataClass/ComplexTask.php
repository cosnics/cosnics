<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass
 */
class ComplexTask extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Task::CONTEXT;
}
