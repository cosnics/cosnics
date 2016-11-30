<?php
namespace Chamilo\Application\Calendar\Architecture;

use Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider;

/**
 *
 * @package Chamilo\Application\Calendar\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ExternalCalendar implements CalendarInterface
{

    /**
     * Get the source type of the implementing context
     * 
     * @return integer
     */
    public function getSourceType()
    {
        return CalendarRendererProvider::SOURCE_TYPE_EXTERNAL;
    }
}