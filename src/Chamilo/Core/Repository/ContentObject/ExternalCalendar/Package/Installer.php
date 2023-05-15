<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass\ExternalCalendar;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalCalendar\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = ExternalCalendar::CONTEXT;
}
