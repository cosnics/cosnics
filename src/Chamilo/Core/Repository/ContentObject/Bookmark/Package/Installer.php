<?php
namespace Chamilo\Core\Repository\ContentObject\Bookmark\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass\Bookmark;

/**
 * @package Chamilo\Core\Repository\ContentObject\Bookmark\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Bookmark::CONTEXT;
}
