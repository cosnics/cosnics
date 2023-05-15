<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;

/**
 * @package Chamilo\Core\Repository\ContentObject\Hotpotatoes\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Hotpotatoes::CONTEXT;
}
