<?php
namespace Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass
 */
class Introduction extends ContentObject implements Versionable
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Introduction';
}
