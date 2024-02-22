<?php
namespace Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass
 */
class Section extends ContentObject implements VersionableInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Section';
}
