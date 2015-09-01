<?php
namespace Chamilo\Libraries\Calendar\Renderer\Interfaces;

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
     * Get the internal events between $start_time and $end_time
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param int $startTime
     * @param int $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEventsInPeriod(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $startTime,
        $endTime, $calculateRecurrence = true);

    /**
     * Get the external events between $start_time and $end_time
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param int $startTime
     * @param int $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getExternalInPeriod(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $startTime, $endTime,
        $calculateRecurrence = true);

    /**
     * Get the events between $start_time and $end_time
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param int $startTime
     * @param int $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEventsInPeriod(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $startTime, $endTime,
        $calculateRecurrence = true);

    /**
     * Get the internal events
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer);

    /**
     * Get the external events
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getExternal(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer);

    /**
     * Get the events
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer);

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

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getDataUser();

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getViewingUser();
}