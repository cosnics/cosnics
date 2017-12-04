<?php
namespace Chamilo\Libraries\Calendar\Event\Renderer;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class EventHtmlTableRenderer extends EventHtmlRenderer
{

    /**
     *
     * @return string
     */
    public function getEventClasses(Event $event)
    {
        $eventClasses = 'event-container';

        if (! $this->getDataProvider()->isSourceVisible($event->getSource()->getTitle()))
        {
            $eventClasses .= ' event-container-hidden';
        }

        return $eventClasses;
    }

    /**
     *
     * @return string
     */
    public function determineEventClasses(Event $event)
    {
        $eventClasses = $this->getEventClasses($event);
        $sourceClasses = $this->getCalendarSources()->getSourceClasses($event->getSource()->getTitle());
        return implode(' ', array($eventClasses, $sourceClasses));
    }

    /**
     *
     * @return string
     */
    public function renderLink(Event $event, $startDate)
    {
        $html = array();

        $fullTitle = $this->renderFullTitle($event, $startDate);

        if ($event->getUrl())
        {
            $html[] = '<a href="' . $event->getUrl() . '" title="' . htmlentities(strip_tags($fullTitle)) . '">';
        }
        else
        {
            $html[] = '<span title="' . htmlentities(strip_tags($fullTitle)) . '">';
        }

        $html[] = $fullTitle;

        if ($event->getUrl())
        {
            $html[] = '</a>';
        }
        else
        {
            $html[] = '</span>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderFullTitle(Event $event, $startDate)
    {
        $fullTitle = '';

        $prefix = $this->renderPrefix($event, $startDate);
        if ($prefix)
        {
            $fullTitle .= $prefix . ' ';
        }

        $fullTitle .= htmlentities($event->getTitle());

        $postfix = $this->renderPostfix($event, $startDate);
        if ($postfix)
        {
            $fullTitle .= ' ' . $postfix;
        }

        return $fullTitle;
    }

    /**
     *
     * @return string
     */
    public function render(Event $event, $startDate)
    {
        $html = array();

        $html[] = $this->renderHeader($event, $startDate);
        $html[] = $this->renderLink($event, $startDate);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderHeader(Event $event)
    {
        $html = array();

        $html[] = '<div class="' . $this->determineEventClasses($event) . '" data-source-key="' .
             $this->getCalendarSources()->getSourceKey($event->getSource()->getTitle()) . '">';
        $html[] = '<div class="event-data">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderFooter()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param integer $date
     * @return string
     */
    public function renderTime($date)
    {
        return date('H:i', $date);
    }

    /**
     *
     * @return string
     */
    public function renderPrefix(Event $event, $startDate)
    {
        if ($this->showPrefixDate($event, $startDate))
        {
            return $this->renderTime($event->getStartDate());
        }
        elseif ($this->showPrefixSymbol($event, $startDate))
        {
            return $this->getPrefixSymbol();
        }
    }

    /**
     *
     * @return string
     */
    public function renderPostfix(Event $event, $startDate)
    {
        if ($this->showPostfixDate($event, $startDate))
        {
            return $this->renderTime($event->getEndDate());
        }
        elseif ($this->showPostFixSymbol($event, $startDate))
        {
            return $this->getPostfixSymbol();
        }
    }

    /**
     *
     * @param string $glyph
     * @return string
     */
    public function getSymbol($glyph)
    {
        $glyph = new FontAwesomeGlyph($glyph);
        return $glyph->render();
    }

    /**
     *
     * @return boolean
     */
    abstract public function showPrefixDate(Event $event, $startDate);

    /**
     *
     * @return boolean
     */
    abstract public function showPrefixSymbol(Event $event, $startDate);

    /**
     *
     * @return string
     */
    abstract public function getPrefixSymbol();

    /**
     *
     * @return boolean
     */
    abstract public function showPostfixDate(Event $event, $startDate);

    /**
     *
     * @return boolean
     */
    abstract public function showPostfixSymbol(Event $event, $startDate);

    /**
     *
     * @return string
     */
    abstract public function getPostfixSymbol();
}
