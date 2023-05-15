<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;

/**
 * @package Chamilo\Core\Repository\ContentObject\Forum\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Forum::CONTEXT;
}
