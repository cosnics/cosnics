<?php
namespace Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass
 */
class Introduction extends ContentObject implements VersionableInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Introduction';
}
