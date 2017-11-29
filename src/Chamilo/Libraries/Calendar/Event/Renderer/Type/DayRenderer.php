<?php
namespace Chamilo\Libraries\Calendar\Event\Renderer\Type;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayRenderer extends HtmlTableRenderer
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
            $this->tableEndDate = strtotime('+1 hour', $this->getStartDate());
        }

        return $this->tableEndDate;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::showPrefixDate()
     */
    public function showPrefixDate()
    {
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        return ($startDate >= $this->getStartDate() && $startDate <= $this->getTableEndDate() &&
             ($startDate != $this->getStartDate() || $endDate < $this->getTableEndDate()));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::showPrefixSymbol()
     */
    public function showPrefixSymbol()
    {
        return ($this->getEvent()->getStartDate() < $this->getStartDate());
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
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        return ($startDate != $endDate) && ($endDate < $this->getTableEndDate() && $startDate < $this->getStartDate());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventTableRenderer::showPostfixSymbol()
     */
    public function showPostfixSymbol()
    {
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