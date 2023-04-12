<?php
namespace Chamilo\Application\Calendar\Architecture;

use Chamilo\Libraries\Calendar\Service\CalendarRendererProvider;

/**
 * @package Chamilo\Application\Calendar\Architecture
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ExternalCalendar implements CalendarInterface
{

    public function getSourceType(): int
    {
        return CalendarRendererProvider::SOURCE_TYPE_EXTERNAL;
    }
}