<?php
namespace Chamilo\Libraries\Calendar\Renderer\Interfaces;

use Chamilo\Libraries\Calendar\Renderer\Renderer;

/**
 * An interface which forces the implementing Application to provide a given set of methods
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CalendarRendererProviderInterface
{

    /**
     * Get the events between $start_time and $end_time which should be displayed in the calendar
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param int $start_time
     * @param int $end_time
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents(Renderer $renderer, $startTime, $endTime);

    /**
     *
     * @return string[]
     */
    public function getDisplayParameters();

    /**
     *
     * @return boolean
     */
    public function supportsVisibility();

    /**
     *
     * @return boolean
     */
    public function supportsActions();
}