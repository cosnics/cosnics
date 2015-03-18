<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;

/**
 * Abstract base-class for the parsing of content object to renderable calendar events
 *
 * @package libraries\calendar\event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EventParser
{

    /**
     *
     * @var \core\repository\ContentObject
     */
    private $content_object;

    /**
     *
     * @var int
     */
    private $start_date;

    /**
     *
     * @var int
     */
    private $end_date;

    /**
     *
     * @var string
     */
    private $event_class_name;

    /**
     *
     * @param \core\repository\ContentObject $content_object
     * @param int $start_date
     * @param int $end_date
     * @param string $event_class_name
     */
    public function __construct($content_object, $start_date = 0, $end_date = 0, $event_class_name)
    {
        $this->content_object = $content_object;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->event_class_name = $event_class_name;
    }

    /**
     *
     * @param \core\repository\ContentObject $content_object
     * @param int $start_date
     * @param int $end_date
     * @param string $event_class_name
     * @return EventParser
     */
    public static function factory($content_object, $start_date = 0, $end_date = 0, $event_class_name)
    {
        $type = $content_object->package();
        $class = $type . '\Integration\Chamilo\Libraries\Calendar\Event\EventParser';

        if (! class_exists($class))
        {
            $message = array();
            $message[] = Translation :: get('ComponentFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $class . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get('EventParser') . '</li>';
            $message[] = '<li>' . Translation :: get($type, null, 'core\\repository\\content_object\\' . $type) . '</li>';
            $message[] = '</ul>';

            throw new ClassNotExistException($class);
        }
        return new $class($content_object, $start_date, $end_date, $event_class_name);
    }

    /**
     *
     * @return \core\repository\ContentObject
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    /**
     *
     * @param \core\repository\ContentObject $content_object
     */
    public function set_content_object(ContentObject $content_object)
    {
        $this->content_object = $content_object;
    }

    /**
     *
     * @return int
     */
    public function get_start_date()
    {
        return $this->start_date;
    }

    /**
     *
     * @param int $start_date
     */
    public function set_start_date($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     *
     * @return int
     */
    public function get_end_date()
    {
        return $this->end_date;
    }

    /**
     *
     * @param int $end_date
     */
    public function set_end_date($end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     *
     * @return string
     */
    public function get_event_class_name()
    {
        return $this->event_class_name;
    }

    /**
     *
     * @param string $event_class_name
     */
    public function set_event_class_name($event_class_name)
    {
        $this->event_class_name = $event_class_name;
    }

    /**
     *
     * @return \application\personal_calendar\Event
     */
    public function get_event_instance()
    {
        $event_class_name = $this->get_event_class_name();

        if (! $event_class_name)
        {
            throw new \Exception(
                'Please implement a local extension of the Event class in your context (' .
                     ClassnameUtilities :: getInstance()->getNamespaceFromClassname($event_class_name) . ')');
        }
        else
        {
            $event = new $event_class_name();

            if (! $event instanceof ContentObjectSupport)
            {
                throw new \Exception(
                    'Your event class (' . $event_class_name .
                         ') does not seem to support content objects, please implement the EventContentObjectSupport interface');
            }
            else
            {
                return $event;
            }
        }
    }

    /**
     *
     * @return Event[]
     */
    abstract public function get_events();
}
