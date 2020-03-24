<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass\ExternalCalendar;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalCalendar\Common
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ExternalCalendarDifference extends ContentObjectDifference
{

    /**
     * @return string[]
     */
    public function getAdditionalPropertyNames()
    {
        return array(ExternalCalendar::PROPERTY_PATH);
    }
}
