<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;

/**
 * @package Chamilo\Core\Repository\ContentObject\Task\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Task::CONTEXT;
}
