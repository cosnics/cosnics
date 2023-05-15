<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link;

/**
 * @package Chamilo\Core\Repository\ContentObject\Link\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Link::CONTEXT;
}
