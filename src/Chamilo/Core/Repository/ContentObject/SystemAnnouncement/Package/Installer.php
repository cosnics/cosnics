<?php
namespace Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;

/**
 * @package Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = SystemAnnouncement::CONTEXT;
}
