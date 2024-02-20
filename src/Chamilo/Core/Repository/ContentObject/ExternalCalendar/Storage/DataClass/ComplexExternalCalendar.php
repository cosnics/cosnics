<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.external_calendar
 */
class ComplexExternalCalendar extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = ExternalCalendar::CONTEXT;
}
