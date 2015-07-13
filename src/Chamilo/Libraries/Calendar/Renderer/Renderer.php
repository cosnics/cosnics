<?php
namespace Chamilo\Libraries\Calendar\Renderer;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Abstract calendar renderer base class
 *
 * @package libraries\calendar\renderer
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
    const TYPE_MINI_DAY = 'Mini_day';
    const TYPE_MINI_MONTH = 'Mini_month';
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
    private $display_time;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Legend
     */
    private $legend;

    /**
     *
     * @var string
     */
    private $link_target;

    /**
     *
     * @param CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\Renderer\Legend $legend
     * @param integer $display_time
     * @param string $link_target
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, Legend $legend, $display_time,
        $link_target = '')
    {
        if (! $dataProvider instanceof CalendarRendererProviderInterface)
        {
            throw new \Exception('Please implement the CalendarRendererProviderInterface in ' . get_class($dataProvider));
        }

        $this->dataProvider = $dataProvider;
        $this->legend = $legend;
        $this->display_time = $display_time;
        $this->link_target = $link_target;
    }

    /**
     *
     * @return string
     */
    public function get_link_target()
    {
        return $this->link_target;
    }

    /**
     *
     * @param string $value
     */
    public function set_link_target($link_target)
    {
        $this->link_target = $link_target;
    }

    /**
     *
     * @return int
     */
    public function get_time()
    {
        return $this->display_time;
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
     *
     * @return \core\user\User
     */
    public function get_user()
    {
        return $this->dataProvider->getDataUser();
    }

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
     * @param int $start_time
     * @param int $end_time
     * @return Event[]
     */
    public function get_events(Renderer $renderer, $start_time, $end_time)
    {
        return $this->getDataProvider()->getEvents($renderer, $start_time, $end_time);
    }

    /**
     * Get the actions available in the renderer for the given event
     *
     * @param Event $event
     * @return \libraries\format\ToolbarItem[]
     */
    public function get_actions(Event $event)
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
     * @return \libraries\format\ToolbarItem[]
     */
    public static function getToolbarItems($types, $type_url, $today_url)
    {
        $items = array();

        foreach ($types as $type)
        {
            $type_name = (string) StringUtilities :: getInstance()->createString($type)->upperCamelize();

            $items[] = new ToolbarItem(
                Translation :: get($type_name . 'View', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Renderer/Type/' . $type),
                str_replace(self :: MARKER_TYPE, $type, $type_url));
        }

        $items[] = new ToolbarItem(
            Translation :: get('Today', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Renderer/Today'),
            $today_url);

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
