<?php
namespace Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass
 */
class Page extends ContentObject implements Versionable, Includeable
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Page';
}
