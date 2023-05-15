<?php
namespace Chamilo\Core\Repository\ContentObject\Introduction\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;

/**
 * @package Chamilo\Core\Repository\ContentObject\Introduction\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Introduction::CONTEXT;
}
