<?php
namespace Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\IncludeableInterface;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass
 */
class Page extends ContentObject implements VersionableInterface, IncludeableInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Page';
}
