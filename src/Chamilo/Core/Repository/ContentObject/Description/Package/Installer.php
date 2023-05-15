<?php
namespace Chamilo\Core\Repository\ContentObject\Description\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass\Description;

/**
 * @package Chamilo\Core\Repository\ContentObject\Description\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Description::CONTEXT;
}
