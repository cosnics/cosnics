<?php
namespace Chamilo\Libraries\Calendar\Renderer;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarDataProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\NotificationMessage;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

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
     * @var Application
     */
    private $dataProvider;

    /**
     * The time of the moment to render
     */
    private $display_time;

    /**
     *
     * @var string[]
     */
    private $legend;

    /**
     *
     * @var string
     */
    private $link_target;

    /**
     *
     * @param CalendarDataProviderInterface $dataProvider
     * @param int $display_time
     * @param string $link_target
     */
    public function __construct(CalendarDataProviderInterface $dataProvider, $display_time, $link_target = '')
    {
        if (! $dataProvider instanceof CalendarDataProviderInterface)
        {
            throw new \Exception('Please implement the CalendarDataProviderInterface in ' . get_class($dataProvider));
        }

        $this->dataProvider = $dataProvider;
        $this->display_time = $display_time;
        $this->link_target = $link_target;
        $this->legend = array();
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
     * @return CalendarDataProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * Render the calendar
     *
     * @return string
     */
    abstract public function render();

    /**
     * Retrieves a color
     *
     * @param string $key
     * @param boolean $fade
     * @return string
     */
    public function get_color_classes($key = null, $fade = false)
    {
        if (is_null($key))
        {
            $key = Translation :: get('MyAgenda');
        }

        if (! in_array($key, $this->legend))
        {
            $this->legend[] = $key;
        }

        $key_id = array_search($key, $this->legend);

        if ($fade)
        {
            return 'event-source-identifier event-source-identifier-' . $key_id . ' event-source-identifier-faded ';
        }
        else
        {
            return 'event-source-identifier event-source-identifier-' . $key_id;
        }
    }

    /**
     * Builds a color-based legend for the calendar to help users to see which dataProviders and locations are the
     * origin
     * of the the published events
     *
     * @return string
     */
    public function build_legend()
    {
        $result = array();

        if (count($this->legend) > 0)
        {
            $visible_sources = 0;

            $result[] = '<fieldset class="event-legend-container" name="test">';
            $result[] = '<legend class="event-legend-label">' . Translation :: get('Legend') . '</legend>';
            $result[] = '<div class="event-legend">';

            foreach ($this->legend as $key_id => $key)
            {
                $event_classes = $this->get_color_classes($key, ! $this->is_source_visible($key));

                $result[] = '<div class="event">';
                $result[] = '<div data-source="' . $key . '" class="' . $event_classes . '">';

                $result[] = $key;

                $result[] = '</div>';
                $result[] = '</div>';

                if ($this->is_source_visible($key))
                {
                    $visible_sources ++;
                }
            }

            $result[] = '</div>';
            $result[] = '<div class="clear"><</div>';
            $result[] = '</fieldset>';

            if ($this->getDataProvider()->supportsVisibility())
            {
                $ajax_visibility_class_name = ClassnameUtilities :: getInstance()->getNamespaceParent(
                    $this->getDataProvider()->getVisibilityContext()) . '\Ajax\Component\CalendarEventVisibilityComponent';

                if (! class_exists($ajax_visibility_class_name))
                {
                    throw new \Exception(
                        'Please add an ajax Class CalendarEventVisibilityComponent to your implementing context\'s Ajax subpackage (' .
                             $this->getDataProvider()->getVisibilityContext() .
                             '). This class should extend the abstract \Chamilo\Libraries\Calendar\Event\Ajax\Component\CalendarEventVisibilityComponent class.');
                }

                $result[] = '<script type="text/javascript">';

                $calendarVisibilityContext = ClassnameUtilities :: getInstance()->getNamespaceParent(
                    $this->getDataProvider()->getVisibilityContext()) . '\Ajax';

                $result[] = 'var calendarVisibilityContext = ' . json_encode($calendarVisibilityContext) . ';';
                $result[] = 'var calendarVisibilityData = ' . json_encode($this->getDataProvider()->getVisibilityData()) .
                     ';';
                $result[] = '</script>';

                $result[] = ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->getJavascriptPath(__NAMESPACE__, true) . 'Highlight.js');
            }

            if (count($this->legend) > 0 && $visible_sources == 0)
            {
                $messages = Session :: retrieve(Application :: PARAM_MESSAGES);
                $messages[Application :: PARAM_MESSAGE_TYPE][] = NotificationMessage :: TYPE_WARNING;
                $messages[Application :: PARAM_MESSAGE][] = Translation :: get('AllEventSourcesHidden');

                Session :: register(Application :: PARAM_MESSAGES, $messages);
            }
        }

        return implode(PHP_EOL, $result);
    }

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
     * @return boolean
     */
    public function is_source_visible($source)
    {
        return ($this->getDataProvider()->supportsVisibility() && $this->getDataProvider()->isSourceVisible($source)) ||
             (! $this->getDataProvider()->supportsVisibility());
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
     * @return Renderer
     */
    public static function factory()
    {
        $arguments = func_get_args();
        $type = array_shift($arguments);

        $class_name = static :: context() . '\Type\\' .
             StringUtilities :: getInstance()->createString($type)->upperCamelize() . static :: class_name(false);

        $class = new \ReflectionClass($class_name);
        return $class->newInstanceArgs($arguments);
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
