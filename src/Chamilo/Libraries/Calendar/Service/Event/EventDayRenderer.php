<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventDayRenderer extends EventTableRenderer
{

    /**
     *
     * @var integer
     */
    private $tableEndDate;

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Service\Event\EventTableRenderer::getPostfixSymbol()
     */
    public function getPostfixSymbol()
    {
        return $this->getSymbol('chevron-down');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Service\Event\EventTableRenderer::getPrefixSymbol()
     */
    public function getPrefixSymbol()
    {
        return $this->getSymbol('chevron-up');
    }

    /**
     *
     * @return integer
     */
    public function getTableEndDate()
    {
        if (!isset($this->tableEndDate))
        {
            $configuration = $this->getConfiguration();
            $this->tableEndDate = strtotime(
                '+' . $configuration->getHourStep() . ' hours', $configuration->getStartDate()
            );
        }

        return $this->tableEndDate;
    }

    /**
     * @return boolean
     */
    public function isFadedEvent()
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function showPostfixDate()
    {
        $configuration = $this->getConfiguration();
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        return ($startDate != $endDate) &&
            ($endDate < $this->getTableEndDate() && $startDate < $configuration->getStartDate());
    }

    /**
     * @return boolean
     */
    public function showPostfixSymbol()
    {
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        return ($startDate != $endDate) && ($endDate > $this->getTableEndDate());
    }

    /**
     * @return boolean
     */
    public function showPrefixDate()
    {
        $configuration = $this->getConfiguration();
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        return ($startDate >= $configuration->getStartDate() && $startDate <= $this->getTableEndDate() &&
            ($startDate != $configuration->getStartDate() || $endDate < $this->getTableEndDate()));
    }

    /**
     * @return boolean
     */
    public function showPrefixSymbol()
    {
        return ($this->getEvent()->getStartDate() < $this->getConfiguration()->getStartDate());
    }
}