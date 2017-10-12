<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class EventTableRenderer extends EventRenderer
{

    /**
     *
     * @return string
     */
    public function determineEventClasses()
    {
        $eventClasses = $this->getEventClasses($this->getEvent()->getStartDate());
        $sourceClasses = $this->getRenderer()->getLegend()->getSourceClasses(
            $this->getEvent()->getSource(),
            $this->isFadedEvent());
        return implode(' ', array($eventClasses, $sourceClasses));
    }

    /**
     *
     * @return string
     */
    public function renderLink()
    {
        $html = array();

        $fullTitle = $this->renderFullTitle();

        $html[] = '<a href="' . $this->getEvent()->getUrl() . '" title="' . htmlentities(strip_tags($fullTitle)) . '">';
        $html[] = $fullTitle;
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderFullTitle()
    {
        $fullTitle = '';

        $prefix = $this->renderPrefix();
        if ($prefix)
        {
            $fullTitle .= $prefix . ' ';
        }

        $fullTitle .= htmlentities($this->getEvent()->getTitle());

        $postfix = $this->renderPostfix();
        if ($postfix)
        {
            $fullTitle .= ' ' . $postfix;
        }

        return $fullTitle;
    }

    /**
     *
     * @return boolean
     */
    abstract public function isFadedEvent();

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = $this->renderHeader();
        $html[] = $this->renderLink();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderHeader()
    {
        $html = array();

        $html[] = '<div class="' . $this->determineEventClasses() . '" data-source-key="' .
             $this->getRenderer()->getLegend()->addSource($this->getEvent()->getSource()) . '">';
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
    public function renderPrefix()
    {
        if ($this->showPrefixDate())
        {
            return $this->renderTime($this->getEvent()->getStartDate());
        }
        elseif ($this->showPrefixSymbol())
        {
            return $this->getPrefixSymbol();
        }
    }

    /**
     *
     * @return string
     */
    public function renderPostfix()
    {
        if ($this->showPostfixDate())
        {
            return $this->renderTime($this->getEvent()->getEndDate());
        }
        elseif ($this->showPostFixSymbol())
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
    abstract public function showPrefixDate();

    /**
     *
     * @return boolean
     */
    abstract public function showPrefixSymbol();

    /**
     *
     * @return string
     */
    abstract public function getPrefixSymbol();

    /**
     *
     * @return boolean
     */
    abstract public function showPostfixDate();

    /**
     *
     * @return boolean
     */
    abstract public function showPostfixSymbol();

    /**
     *
     * @return string
     */
    abstract public function getPostfixSymbol();
}
