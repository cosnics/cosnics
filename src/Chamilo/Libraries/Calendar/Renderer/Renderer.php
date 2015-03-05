<?php
namespace Chamilo\Libraries\Calendar\Renderer;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRenderer;
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
    const TYPE_DAY = 'day';
    const TYPE_LIST = 'list';
    const TYPE_MINI_DAY = 'mini_day';
    const TYPE_MINI_MONTH = 'mini_month';
    const TYPE_MONTH = 'month';
    const TYPE_WEEK = 'week';
    const TYPE_YEAR = 'year';

    /**
     *
     * @var Application
     */
    private $application;

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
     * @param Application $application
     * @param int $display_time
     * @param string $link_target
     */
    public function __construct(Application $application, $display_time, $link_target = '')
    {
        if (! $application instanceof CalendarRenderer)
        {
            throw new \Exception('Please implement the CalendarRendererSupport interface in ' . get_class($application));
        }

        $this->application = $application;
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
     * @return Application
     */
    public function get_application()
    {
        return $this->application;
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
     * Builds a color-based legend for the calendar to help users to see which applications and locations are the origin
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

            if ($this->implements_visibility())
            {
                $ajax_visibility_class_name = ClassnameUtilities :: getInstance()->getNamespaceParent(
                    $this->get_application()->context()) . '\Ajax\Component\CalendarEventVisibilityComponent';

                if (! class_exists($ajax_visibility_class_name))
                {
                    throw new \Exception(
                        'Please add an ajax Class CalendarEventVisibilityComponent to your implementing context\'s Ajax subpackage (' .
                             $this->get_application()->context() .
                             '). This class should extend the abstract \Chamilo\Libraries\Calendar\Event\Ajax\Component\CalendarEventVisibilityComponent class.');
                }

                $result[] = '<script type="text/javascript">';

                $calendarVisibilityContext = ClassnameUtilities :: getInstance()->getNamespaceParent(
                    $this->get_application()->context()) . '\Ajax';

                $result[] = 'var calendarVisibilityContext = ' . json_encode($calendarVisibilityContext) . ';';
                $result[] = 'var calendarVisibilityData = ' .
                     json_encode($this->get_application()->get_calendar_renderer_visibility_data()) . ';';
                $result[] = '</script>';

                $result[] = ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->namespaceToFullPath(__NAMESPACE__, true) . 'resources/javascript/highlight.js');
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
        return $this->application->get_user();
    }

    /**
     * Check whether the given source is visible for the user
     *
     * @param string $source
     * @return boolean
     */
    public function is_source_visible($source)
    {
        return ($this->get_application() instanceof VisibilitySupport &&
             $this->get_application()->is_calendar_renderer_source_visible($source)) ||
             (! $this->get_application() instanceof VisibilitySupport);
    }

    /**
     * Does the Application support visibility toggling
     *
     * @return boolean
     */
    public function implements_visibility()
    {
        return $this->get_application() instanceof VisibilitySupport;
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
        return $this->get_application()->get_calendar_renderer_events($renderer, $start_time, $end_time);
    }

    /**
     * Get the actions available in the renderer for the given event
     *
     * @param Event $event
     * @return \libraries\format\ToolbarItem[]
     */
    public function get_actions(Event $event)
    {
        if (! $this->get_application() instanceof ActionSupport)
        {
            return array();
        }

        return $this->get_application()->get_calendar_event_actions($event);
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
    public static function get_renderer_toolbar_items($types, $type_url, $today_url)
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
