<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;

/**
 * @package Chamilo\Core\Repository\ContentObject\Webpage\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Webpage::CONTEXT;
}
