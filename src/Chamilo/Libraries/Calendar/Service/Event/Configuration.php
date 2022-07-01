<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Configuration extends ParameterBag
{
    public const PROPERTY_END_DATE = 'endDate';
    public const PROPERTY_HOUR_STEP = 'hourStep';
    public const PROPERTY_START_DATE = 'startDate';

    /**
     *
     * @return int
     */
    public function getEndDate()
    {
        return $this->get(self::PROPERTY_END_DATE);
    }

    /**
     *
     * @return int
     */
    public function getHourStep()
    {
        return $this->get(self::PROPERTY_HOUR_STEP);
    }

    /**
     *
     * @return int
     */
    public function getStartDate()
    {
        return $this->get(self::PROPERTY_START_DATE);
    }

    /**
     *
     * @param int $endDate
     */
    public function setEndDate($endDate)
    {
        $this->set(self::PROPERTY_END_DATE, $endDate);
    }

    /**
     *
     * @param int $hourStep
     */
    public function setHourStep($hourStep)
    {
        $this->set(self::PROPERTY_HOUR_STEP, $hourStep);
    }

    /**
     *
     * @param int $startDate
     */
    public function setStartDate($startDate)
    {
        $this->set(self::PROPERTY_START_DATE, $startDate);
    }
}