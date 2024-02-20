<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.calendar_event
 * @author  Hans De Bisschop
 * @author  Dieter De Neef
 */
class ComplexCalendarEvent extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = CalendarEvent::CONTEXT;
}
