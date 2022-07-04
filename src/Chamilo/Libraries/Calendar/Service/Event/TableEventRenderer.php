<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class TableEventRenderer extends EventRenderer
{

    /**
     * @throws \Exception
     */
    public function render(
        Event $event, int $cellStartDate, int $cellEndDate, bool $isFadedEvent = false,
        bool $isEventSourceVisible = true
    ): string
    {
        $html = [];

        $html[] = $this->renderHeader($event, $isFadedEvent, $isEventSourceVisible);
        $html[] = $this->renderLink($event, $cellStartDate, $cellEndDate);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Exception
     */
    public function determineEventClasses(Event $event, bool $isFadedEvent = false, bool $isEventSourceVisible = true
    ): string
    {
        $eventClasses = $this->getEventClasses($isEventSourceVisible);
        $sourceClasses = $this->getLegendRenderer()->getSourceClasses(
            $event->getSource(), $isFadedEvent
        );

        return implode(' ', [$eventClasses, $sourceClasses]);
    }

    abstract public function getPostfixSymbol(): string;

    abstract public function getPrefixSymbol(): string;

    public function getSymbol(string $glyph): string
    {
        $glyph = new FontAwesomeGlyph($glyph);

        return $glyph->render();
    }

    public function renderFooter(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderFullTitle(Event $event, int $cellStartDate, int $cellEndDate): string
    {
        $fullTitle = '';

        $prefix = $this->renderPrefix($event, $cellStartDate, $cellEndDate);
        if ($prefix)
        {
            $fullTitle .= $prefix . ' ';
        }

        $fullTitle .= htmlentities($event->getTitle());

        $postfix = $this->renderPostfix($event, $cellStartDate, $cellEndDate);
        if ($postfix)
        {
            $fullTitle .= ' ' . $postfix;
        }

        return $fullTitle;
    }

    /**
     * @throws \Exception
     */
    public function renderHeader(Event $event, bool $isFadedEvent = false, bool $isEventSourceVisible = true): string
    {
        $html = [];

        $html[] = '<div class="' . $this->determineEventClasses($event, $isFadedEvent, $isEventSourceVisible) .
            '" data-source-key="' . $this->getLegendRenderer()->addSource($event->getSource()) . '">';
        $html[] = '<div class="event-data">';

        return implode(PHP_EOL, $html);
    }

    public function renderLink(Event $event, int $cellStartDate, int $cellEndDate): string
    {
        $html = [];

        $fullTitle = $this->renderFullTitle($event, $cellStartDate, $cellEndDate);

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

    public function renderPostfix(Event $event, int $cellStartDate, int $cellEndDate): string
    {
        if ($this->showPostfixDate($event, $cellStartDate, $cellEndDate))
        {
            return $this->renderTime($event->getEndDate());
        }
        elseif ($this->showPostfixSymbol($event, $cellStartDate))
        {
            return $this->getPostfixSymbol();
        }

        return '';
    }

    public function renderPrefix(Event $event, int $cellStartDate, int $cellEndDate): string
    {
        if ($this->showPrefixDate($event, $cellStartDate, $cellEndDate))
        {
            return $this->renderTime($event->getStartDate());
        }
        elseif ($this->showPrefixSymbol($event, $cellStartDate))
        {
            return $this->getPrefixSymbol();
        }

        return '';
    }

    public function renderTime(int $date): string
    {
        return date('H:i', $date);
    }

    abstract public function showPostfixDate(Event $event, int $cellStartDate, int $cellEndDate): bool;

    abstract public function showPostfixSymbol(Event $event, int $cellEndDate): bool;

    abstract public function showPrefixDate(Event $event, int $cellStartDate, int $cellEndDate): bool;

    abstract public function showPrefixSymbol(Event $event, int $cellStartDate): bool;
}
