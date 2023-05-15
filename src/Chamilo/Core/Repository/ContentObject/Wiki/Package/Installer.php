<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Wiki\Storage\DataClass\Wiki;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Wiki::CONTEXT;
}
