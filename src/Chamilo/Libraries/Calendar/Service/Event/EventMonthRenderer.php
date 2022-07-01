<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventMonthRenderer extends EventTableRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Service\Event\EventTableRenderer::getPostfixSymbol()
     */
    public function getPostfixSymbol()
    {
        return $this->getSymbol('chevron-right');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Service\Event\EventTableRenderer::getPrefixSymbol()
     */
    public function getPrefixSymbol()
    {
        return $this->getSymbol('chevron-left');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Service\Event\EventTableRenderer::isFadedEvent()
     */
    public function isFadedEvent()
    {
        $startDate = $this->getEvent()->getStartDate();

        $fromDate = strtotime(date('Y-m-1', $this->getRenderer()->getDisplayTime()));
        $toDate = strtotime('-1 Second', strtotime('Next Month', $fromDate));

        return $startDate < $fromDate || $startDate > $toDate;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Service\Event\EventTableRenderer::showPostfixDate()
     */
    public function showPostfixDate()
    {
        $configuration = $this->getConfiguration();
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        return ($startDate != $endDate && $endDate < strtotime('+1 Day', $configuration->getStartDate()) &&
            $startDate < $configuration->getStartDate());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Service\Event\EventTableRenderer::showPostfixSymbol()
     */
    public function showPostfixSymbol()
    {
        $configuration = $this->getConfiguration();
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        return ($startDate != $endDate && $endDate > strtotime('+1 Day', $configuration->getStartDate()));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Service\Event\EventTableRenderer::showPrefixDate()
     */
    public function showPrefixDate()
    {
        $configuration = $this->getConfiguration();
        $startDate = $this->getEvent()->getStartDate();

        return ($startDate >= $configuration->getStartDate() &&
            $startDate <= strtotime('+1 Day', $configuration->getStartDate()) &&
            $startDate != $configuration->getStartDate());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Service\Event\EventTableRenderer::showPrefixSymbol()
     */
    public function showPrefixSymbol()
    {
        return ($this->getEvent()->getStartDate() < $this->getConfiguration()->getStartDate());
    }
}
