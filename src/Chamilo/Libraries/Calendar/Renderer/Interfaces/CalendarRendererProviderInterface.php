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
     * Get the internal events between $startTime and $endTime
     *
     * @param integer $startTime
     * @param integer $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEventsInPeriod($startTime, $endTime, $calculateRecurrence = true);

    /**
     * Get the external events between $startTime and $endTime
     *
     * @param integer $startTime
     * @param integer $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getExternalEventsInPeriod($startTime, $endTime, $calculateRecurrence = true);

    /**
     * Get the events between $startTime and $endTime
     *
     * @param integer $startTime
     * @param integer $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEventsInPeriod($startTime, $endTime, $calculateRecurrence = true);

    /**
     * Get the internal events
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEvents();

    /**
     * Get the external events
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getExternalEvents();

    /**
     * Get the events
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEvents();

    /**
     *
     * @return string[]
     */
    public function getDisplayParameters();

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