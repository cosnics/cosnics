<?php
namespace Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass
 */
class ComplexNote extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Note::CONTEXT;
}
