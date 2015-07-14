<?php
namespace Chamilo\Libraries\Calendar\Renderer;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Renderer
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

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
     *
     * @var CalendarRendererProviderInterface
     */
    private $dataProvider;

    /**
     * The time of the moment to render
     */
    private $displayTime;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Legend
     */
    private $legend;

    /**
     *
     * @var string
     */
    private $linkTarget;

    /**
     *
     * @param CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\Renderer\Legend $legend
     * @param integer $display_time
     * @param string $link_target
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, Legend $legend, $displayTime,
        $linkTarget = '')
    {
        if (! $dataProvider instanceof CalendarRendererProviderInterface)
        {
            throw new \Exception('Please implement the CalendarRendererProviderInterface in ' . get_class($dataProvider));
        }

        $this->dataProvider = $dataProvider;
        $this->legend = $legend;
        $this->displayTime = $displayTime;
        $this->linkTarget = $linkTarget;
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
     * @return CalendarRendererProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
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
     * Render the calendar
     *
     * @return string
     */
    abstract public function render();

    /**
     * Check whether the given source is visible for the user
     *
     * @param string $source
     * @param integer $userIdentifier
     * @return boolean
     */
    public function isSourceVisible($source, $userIdentifier)
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
     * @param Renderer $renderer
     * @param int $startTime
     * @param int $endTime
     * @return Event[]
     */
    public function getEvents(Renderer $renderer, $startTime, $endTime)
    {
        return $this->getDataProvider()->getEvents($renderer, $startTime, $endTime);
    }

    /**
     * Get the actions available in the renderer for the given event
     *
     * @param Event $event
     * @return \libraries\format\ToolbarItem[]
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
     * @param string $url
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public static function getToolbarItems($types, $typeUrl, $todayUrl)
    {
        $items = array();

        foreach ($types as $type)
        {
            $items[] = new ToolbarItem(
                Translation :: get($type . 'View', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Renderer/Type/' . $type),
                str_replace(self :: MARKER_TYPE, $type, $typeUrl));
        }

        $items[] = new ToolbarItem(
            Translation :: get('Today', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Renderer/Today'),
            $todayUrl);

        return $items;
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
