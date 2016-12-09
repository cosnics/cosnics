<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Configuration
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Configuration extends ParameterBag
{
    const PROPERTY_START_DATE = 'startDate';
    const PROPERTY_END_DATE = 'endDate';
    const PROPERTY_HOUR_STEP = 'hourStep';

    /**
     *
     * @return integer
     */
    public function getStartDate()
    {
        return $this->get(self :: PROPERTY_START_DATE);
    }

    /**
     *
     * @param integer $startDate
     */
    public function setStartDate($startDate)
    {
        return $this->set(self :: PROPERTY_START_DATE, $startDate);
    }

    /**
     *
     * @return integer
     */
    public function getEndDate()
    {
        return $this->get(self :: PROPERTY_END_DATE);
    }

    /**
     *
     * @param integer $endDate
     */
    public function setEndDate($endDate)
    {
        return $this->set(self :: PROPERTY_END_DATE, $endDate);
    }

    /**
     *
     * @return integer
     */
    public function getHourStep()
    {
        return $this->get(self :: PROPERTY_HOUR_STEP);
    }

    /**
     *
     * @param integer $hourStep
     */
    public function setHourStep($hourStep)
    {
        return $this->set(self :: PROPERTY_HOUR_STEP, $hourStep);
    }
}