<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event\Type;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
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
     * @return integer
     */
    public function getTableEndDate()
    {
        if (! isset($this->tableEndDate))
        {
            $configuration = $this->getConfiguration();
            $this->tableEndDate = strtotime(
                '+' . $configuration->getHourStep() . ' hours',
                $configuration->getStartDate());
        }

        return $this->tableEndDate;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::showPrefixDate()
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
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::showPrefixSymbol()
     */
    public function showPrefixSymbol()
    {
        return ($this->getEvent()->getStartDate() < $this->getConfiguration()->getStartDate());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::getPrefixSymbol()
     */
    public function getPrefixSymbol()
    {
        return $this->getSymbol('chevron-up');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::showPostfixDate()
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
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::showPostfixSymbol()
     */
    public function showPostfixSymbol()
    {
        $configuration = $this->getConfiguration();
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        return ($startDate != $endDate) && ($endDate > $this->getTableEndDate());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::getPostfixSymbol()
     */
    public function getPostfixSymbol()
    {
        return $this->getSymbol('chevron-down');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::isFadedEvent()
     */
    public function isFadedEvent()
    {
        return false;
    }
}