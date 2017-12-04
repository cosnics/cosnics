<?php
namespace Chamilo\Libraries\Calendar\Event\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\Renderer\EventHtmlTableRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayRenderer extends EventHtmlTableRenderer
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
    public function getTableEndDate($startDate)
    {
        if (! isset($this->tableEndDate))
        {
            $this->tableEndDate = strtotime('+1 hour', $startDate);
        }

        return $this->tableEndDate;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer::showPrefixDate()
     */
    public function showPrefixDate(Event $event, $startDate)
    {
        $eventStartDate = $event->getStartDate();
        $eventEndDate = $event->getEndDate();

        return ($eventStartDate >= $startDate && $eventStartDate <= $this->getTableEndDate($startDate) &&
             ($eventStartDate != $startDate || $eventEndDate < $this->getTableEndDate($startDate)));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer::showPrefixSymbol()
     */
    public function showPrefixSymbol(Event $event, $startDate)
    {
        return ($event->getStartDate() < $startDate);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer::getPrefixSymbol()
     */
    public function getPrefixSymbol()
    {
        return $this->getSymbol('chevron-up');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer::showPostfixDate()
     */
    public function showPostfixDate(Event $event, $startDate)
    {
        $eventStartDate = $event->getStartDate();
        $eventEndDate = $event->getEndDate();

        return ($eventStartDate != $eventEndDate) &&
             ($eventEndDate < $this->getTableEndDate($startDate) && $eventStartDate < $startDate);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer::showPostfixSymbol()
     */
    public function showPostfixSymbol(Event $event, $startDate)
    {
        $eventStartDate = $event->getStartDate();
        $eventEndDate = $event->getEndDate();

        return ($eventStartDate != $eventEndDate) && ($eventEndDate > $this->getTableEndDate($startDate));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer::getPostfixSymbol()
     */
    public function getPostfixSymbol()
    {
        return $this->getSymbol('chevron-down');
    }
}