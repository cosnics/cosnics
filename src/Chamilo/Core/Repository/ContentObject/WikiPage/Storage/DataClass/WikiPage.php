<?php
namespace Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ForcedVersionSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * @package Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass
 */
class WikiPage extends ContentObject implements Versionable, ForcedVersionSupport
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\WikiPage';
}
