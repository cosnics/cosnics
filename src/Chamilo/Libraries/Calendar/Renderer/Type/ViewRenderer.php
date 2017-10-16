<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ViewRenderer extends Renderer
{
    // Parameters
    const PARAM_TIME = 'time';
    const PARAM_TYPE = 'type';

    // Markers
    const MARKER_TYPE = '__TYPE__';

    // Types
    const TYPE_DAY = 'Day';
    const TYPE_LIST = 'List';
    const TYPE_MINI_DAY = 'MiniDay';
    const TYPE_MINI_MONTH = 'MiniMonth';
    const TYPE_MONTH = 'Month';
    const TYPE_WEEK = 'Week';
    const TYPE_YEAR = 'Year';

    /**
     * The time of the moment to render
     *
     * @var integer
     */
    private $displayTime;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Legend
     */
    private $legend;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    private $viewActions;

    /**
     *
     * @var string
     */
    private $linkTarget;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\Renderer\Legend $legend
     * @param integer $displayTime
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     * @param string $linkTarget
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, Legend $legend, $displayTime,
        $viewActions = array(), $linkTarget = '')
    {
        parent::__construct($dataProvider);

        $this->legend = $legend;
        $this->displayTime = $displayTime;
        $this->viewActions = $viewActions;
        $this->linkTarget = $linkTarget;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getViewActions()
    {
        return $this->viewActions;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     */
    public function setViewActions($viewActions)
    {
        $this->viewActions = $viewActions;
    }

    /**
     *
     * @return string
     */
    public function getLinkTarget()
    {
        return $this->linkTarget;
    }

    /**
     *
     * @param string $value
     */
    public function setLinkTarget($linkTarget)
    {
        $this->linkTarget = $linkTarget;
    }

    /**
     *
     * @return integer
     */
    public function getDisplayTime()
    {
        return $this->displayTime;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Legend
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Legend $legend
     */
    public function setLegend(Legend $legend)
    {
        $this->legend = $legend;
    }

    /**
     * Check whether the given source is visible for the user
     *
     * @param string $source
     * @param integer $userIdentifier
     * @return boolean
     */
    public function isSourceVisible($source, $userIdentifier = null)
    {
        if ($this->getDataProvider()->supportsVisibility())
        {
            return $this->getDataProvider()->isSourceVisible($source, $userIdentifier);
        }

        return true;
    }

    /**
     * Get the events between $start_time and $end_time which should be displayed in the calendar
     *
     * @param int $startTime
     * @param int $endTime
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents($startTime, $endTime)
    {
        $events = $this->getDataProvider()->getAllEventsInPeriod($startTime, $endTime);

        usort(
            $events,
            function ($eventLeft, $eventRight)
            {
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
            });

        return $events;
    }

    /**
     * Get the actions available in the renderer for the given event
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getActions(Event $event)
    {
        if (! $this->getDataProvider() instanceof ActionSupport)
        {
            return array();
        }

        return $this->getDataProvider()->getEventActions($event);
    }

    /**
     *
     * @param string[] $types
     * @param string $typeUrl
     * @param string $todayUrl
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public static function getToolbarItems($types, $typeUrl, $todayUrl)
    {
        $items = array();

        foreach ($types as $type)
        {
            $items[] = new ToolbarItem(
                Translation::get($type . 'View', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getImagePath('Chamilo\Libraries\Calendar\Renderer', 'Renderer/Type/' . $type),
                str_replace(self::MARKER_TYPE, $type, $typeUrl));
        }

        $items[] = new ToolbarItem(
            Translation::get('Today', null, Utilities::COMMON_LIBRARIES),
            Theme::getInstance()->getImagePath('Chamilo\Libraries\Calendar\Renderer', 'Renderer/Today'),
            $todayUrl);

        return $items;
    }

    /**
     *
     * @param string[] $types
     * @param string $typeUrl
     * @param string $todayUrl
     * @return \Chamilo\Libraries\Format\Tabs\DynamicVisualTab[]
     */
    public static function getTabs($types, $typeUrl, $todayUrl)
    {
        $tabs = array();

        foreach ($types as $type)
        {
            $tabs[] = new DynamicVisualTab(
                $type,
                Translation::get($type . 'View', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getImagePath('Chamilo\Libraries\Calendar\Renderer', 'Renderer/Tab/Type/' . $type),
                str_replace(self::MARKER_TYPE, $type, $typeUrl),
                false,
                false,
                DynamicVisualTab::POSITION_LEFT,
                DynamicVisualTab::DISPLAY_BOTH_SELECTED);
        }

        $tabs[] = new DynamicVisualTab(
            'today',
            Translation::get('Today', null, Utilities::COMMON_LIBRARIES),
            Theme::getInstance()->getImagePath('Chamilo\Libraries\Calendar\Renderer', 'Renderer/Tab/Today'),
            $todayUrl,
            false,
            false,
            DynamicVisualTab::POSITION_LEFT,
            DynamicVisualTab::DISPLAY_BOTH_SELECTED);

        return $tabs;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    public function renderTypeButton()
    {
        $rendererTypes = array(
            ViewRenderer::TYPE_MONTH,
            ViewRenderer::TYPE_WEEK,
            ViewRenderer::TYPE_DAY,
            ViewRenderer::TYPE_LIST);

        $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $currentRendererType = $displayParameters[self::PARAM_TYPE];

        $button = new DropdownButton(Translation::get($currentRendererType . 'View'), new FontAwesomeGlyph('calendar'));
        $button->setDropdownClasses('dropdown-menu-right');

        foreach ($rendererTypes as $rendererType)
        {
            $displayParameters[self::PARAM_TYPE] = $rendererType;
            $typeUrl = new Redirect($displayParameters);

            $button->addSubButton(
                new SubButton(
                    Translation::get($rendererType . 'View'),
                    null,
                    $typeUrl->getUrl(),
                    SubButton::DISPLAY_LABEL,
                    false,
                    $currentRendererType == $rendererType ? 'selected' : 'not-selected'));
        }

        return $button;
    }

    /**
     *
     * @return string
     */
    public function determineNavigationUrl()
    {
        $parameters = $this->getDataProvider()->getDisplayParameters();
        $parameters[self::PARAM_TIME] = Calendar::TIME_PLACEHOLDER;

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }

    /**
     *
     * @return string
     */
    public function renderViewActions()
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        foreach ($this->getViewActions() as $viewAction)
        {
            $buttonToolBar->addItem($viewAction);
        }

        $buttonToolBar->addItem($this->renderTypeButton());

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }
}
