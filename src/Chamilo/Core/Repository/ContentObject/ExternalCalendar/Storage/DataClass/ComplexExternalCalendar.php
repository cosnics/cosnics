<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.external_calendar
 */
class ComplexExternalCalendar extends ComplexContentObjectItem implements CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = ExternalCalendar::CONTEXT;
}
