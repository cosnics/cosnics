<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assessment\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Assessment::CONTEXT;
}
