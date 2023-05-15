<?php
namespace Chamilo\Core\Repository\ContentObject\Announcement\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement;

/**
 * @package Chamilo\Core\Repository\ContentObject\Announcement\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Announcement::CONTEXT;
}
