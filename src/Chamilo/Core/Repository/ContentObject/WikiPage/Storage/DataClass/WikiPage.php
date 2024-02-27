<?php
namespace Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ForcedVersionSupportInterface;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass
 */
class WikiPage extends ContentObject
    implements VersionableInterface, ForcedVersionSupportInterface, DataClassVirtualExtensionInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\WikiPage';
}
