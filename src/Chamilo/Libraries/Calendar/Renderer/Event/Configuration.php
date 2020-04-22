<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Configuration extends ParameterBag
{
    const PROPERTY_END_DATE = 'endDate';

    const PROPERTY_HOUR_STEP = 'hourStep';

    const PROPERTY_START_DATE = 'startDate';

    /**
     *
     * @return integer
     */
    public function getEndDate()
    {
        return $this->get(self::PROPERTY_END_DATE);
    }

    /**
     *
     * @return integer
     */
    public function getHourStep()
    {
        return $this->get(self::PROPERTY_HOUR_STEP);
    }

    /**
     *
     * @return integer
     */
    public function getStartDate()
    {
        return $this->get(self::PROPERTY_START_DATE);
    }

    /**
     *
     * @param integer $endDate
     */
    public function setEndDate($endDate)
    {
        $this->set(self::PROPERTY_END_DATE, $endDate);
    }

    /**
     *
     * @param integer $hourStep
     */
    public function setHourStep($hourStep)
    {
        $this->set(self::PROPERTY_HOUR_STEP, $hourStep);
    }

    /**
     *
     * @param integer $startDate
     */
    public function setStartDate($startDate)
    {
        $this->set(self::PROPERTY_START_DATE, $startDate);
    }
}