<?php
namespace Chamilo\Core\Repository\ContentObject\BlogItem\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\BlogItem;

/**
 * @package Chamilo\Core\Repository\ContentObject\BlogItem\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = BlogItem::CONTEXT;
}
