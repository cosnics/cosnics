<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\VisibilitySupport;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class HtmlCalendarRenderer extends CalendarRenderer
{
    public const MARKER_TYPE = '__TYPE__';

    public const PARAM_TIME = 'time';
    public const PARAM_TYPE = 'type';

    public const TYPE_DAY = 'Day';
    public const TYPE_LIST = 'List';
    public const TYPE_MINI_DAY = 'MiniDay';
    public const TYPE_MINI_MONTH = 'MiniMonth';
    public const TYPE_MONTH = 'Month';
    public const TYPE_WEEK = 'Week';
    public const TYPE_YEAR = 'Year';

    private LegendRenderer $legendRenderer;

    public function __construct(LegendRenderer $legendRenderer)
    {
        $this->legendRenderer = $legendRenderer;
    }

    public function determineNavigationUrl(): string
    {
        $parameters = $this->getDataProvider()->getDisplayParameters();
        $parameters[self::PARAM_TIME] = CalendarTable::TIME_PLACEHOLDER;

        $redirect = new Redirect($parameters);

        return $redirect->getUrl();
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getActions(Event $event): array
    {
        if ($this->getDataProvider() instanceof ActionSupport)
        {
            return $this->getDataProvider()->getEventActions($event);
        }

        return [];
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents(int $startTime, int $endTime): array
    {
        $events = $this->getDataProvider()->getAllEventsInPeriod($startTime, $endTime);

        usort(
            $events, function (Event $eventLeft, Event $eventRight) {
            if ($eventLeft->getStartDate() < $eventRight->getStartDate())
            {
                return - 1;
            }
            elseif ($eventLeft->getStartDate() > $eventRight->getStartDate())
            {
                return 1;
            }
            else
            {
                return 0;
            }
        }
        );

        return $events;
    }

    public function getLegendRenderer(): LegendRenderer
    {
        return $this->legendRenderer;
    }

    public function setLegendRenderer(LegendRenderer $legendRenderer)
    {
        $this->legendRenderer = $legendRenderer;
    }

    public function isSourceVisible(string $source, ?int $userIdentifier = null): bool
    {
        if ($this->getDataProvider() instanceof VisibilitySupport)
        {
            return $this->getDataProvider()->isSourceVisible($source, $userIdentifier);
        }

        return true;
    }

    public function renderTypeButton(): DropdownButton
    {
        $rendererTypes = [
            HtmlCalendarRenderer::TYPE_MONTH,
            HtmlCalendarRenderer::TYPE_WEEK,
            HtmlCalendarRenderer::TYPE_DAY,
            HtmlCalendarRenderer::TYPE_LIST
        ];

        $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $currentRendererType = $displayParameters[self::PARAM_TYPE];

        $button = new DropdownButton(
            Translation::get($currentRendererType . 'View'), new FontAwesomeGlyph('calendar-alt'),
            AbstractButton::DISPLAY_ICON_AND_LABEL, [], ['dropdown-menu-right']
        );

        foreach ($rendererTypes as $rendererType)
        {
            $displayParameters[self::PARAM_TYPE] = $rendererType;
            $typeUrl = new Redirect($displayParameters);

            $button->addSubButton(
                new SubButton(
                    Translation::get($rendererType . 'View'), null, $typeUrl->getUrl(), AbstractButton::DISPLAY_LABEL,
                    null, [], null, $currentRendererType == $rendererType
                )
            );
        }

        return $button;
    }

    /**
     * @throws \ReflectionException
     */
    public function renderViewActions(): string
    {
        $buttonToolBar = new ButtonToolBar();

        foreach ($this->getViewActions() as $viewAction)
        {
            $buttonToolBar->addItem($viewAction);
        }

        $buttonToolBar->addItem($this->renderTypeButton());

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }
}
