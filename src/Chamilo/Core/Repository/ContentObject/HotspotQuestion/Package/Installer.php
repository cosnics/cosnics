<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\HotspotQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\HotspotQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = HotspotQuestion::CONTEXT;
}
