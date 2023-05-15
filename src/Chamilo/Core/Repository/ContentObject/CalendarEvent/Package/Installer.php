<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;

/**
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = CalendarEvent::CONTEXT;
}
