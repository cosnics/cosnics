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
     * @throws \Exception
     */
    public function render()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderLink();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     * @throws \Exception
     */
    public function determineEventClasses()
    {
        $eventClasses = $this->getEventClasses();
        $sourceClasses = $this->getRenderer()->getLegend()->getSourceClasses(
            $this->getEvent()->getSource(), $this->isFadedEvent()
        );

        return implode(' ', array($eventClasses, $sourceClasses));
    }

    /**
     *
     * @return string
     */
    abstract public function getPostfixSymbol();

    /**
     *
     * @return string
     */
    abstract public function getPrefixSymbol();

    /**
     *
     * @param string $glyph
     *
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
    abstract public function isFadedEvent();

    /**
     *
     * @return string
     */
    public function renderFooter()
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';

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
     * @return string
     * @throws \Exception
     */
    public function renderHeader()
    {
        $html = [];

        $html[] = '<div class="' . $this->determineEventClasses() . '" data-source-key="' .
            $this->getRenderer()->getLegend()->addSource($this->getEvent()->getSource()) . '">';
        $html[] = '<div class="event-data">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderLink()
    {
        $html = [];

        $fullTitle = $this->renderFullTitle();

        if ($this->getEvent()->getUrl())
        {
            $html[] =
                '<a href="' . $this->getEvent()->getUrl() . '" title="' . htmlentities(strip_tags($fullTitle)) . '">';
        }
        else
        {
            $html[] = '<span title="' . htmlentities(strip_tags($fullTitle)) . '">';
        }

        $html[] = $fullTitle;

        if ($this->getEvent()->getUrl())
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

        return '';
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

        return '';
    }

    /**
     *
     * @param integer $date
     *
     * @return string
     */
    public function renderTime($date)
    {
        return date('H:i', $date);
    }

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
     * @return boolean
     */
    abstract public function showPrefixDate();

    /**
     *
     * @return boolean
     */
    abstract public function showPrefixSymbol();
}
