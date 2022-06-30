<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\Calendar\Renderer\LegendRenderer;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
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
abstract class ViewRenderer extends Renderer
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

    private int $displayTime;

    private LegendRenderer $legend;

    private string $linkTarget;

    private array $viewActions;

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     *
     * @throws \Exception
     */
    public function __construct(
        CalendarRendererProviderInterface $dataProvider, LegendRenderer $legend, int $displayTime,
        array $viewActions = [], string $linkTarget = ''
    )
    {
        parent::__construct($dataProvider);

        $this->legend = $legend;
        $this->displayTime = $displayTime;
        $this->viewActions = $viewActions;
        $this->linkTarget = $linkTarget;
    }

    public function determineNavigationUrl(): string
    {
        $parameters = $this->getDataProvider()->getDisplayParameters();
        $parameters[self::PARAM_TIME] = Calendar::TIME_PLACEHOLDER;

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

    public function getDisplayTime(): int
    {
        return $this->displayTime;
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

    public function getLegend(): LegendRenderer
    {
        return $this->legend;
    }

    public function setLegend(LegendRenderer $legend)
    {
        $this->legend = $legend;
    }

    public function getLinkTarget(): string
    {
        return $this->linkTarget;
    }

    public function setLinkTarget(string $linkTarget)
    {
        $this->linkTarget = $linkTarget;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getViewActions(): array
    {
        return $this->viewActions;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     */
    public function setViewActions(array $viewActions)
    {
        $this->viewActions = $viewActions;
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
            ViewRenderer::TYPE_MONTH,
            ViewRenderer::TYPE_WEEK,
            ViewRenderer::TYPE_DAY,
            ViewRenderer::TYPE_LIST
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
