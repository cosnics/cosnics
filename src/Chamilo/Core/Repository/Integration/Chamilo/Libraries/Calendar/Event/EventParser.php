<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;
use Chamilo\Libraries\Platform\Translation;

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
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObject;

    /**
     *
     * @var int
     */
    private $startDate;

    /**
     *
     * @var int
     */
    private $endDate;

    /**
     *
     * @var string
     */
    private $eventClassName;

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param int $startDate
     * @param int $endDate
     * @param string $eventClassName
     */
    public function __construct($contentObject, $startDate = 0, $endDate = 0, $eventClassName)
    {
        $this->contentObject = $contentObject;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->eventClassName = $eventClassName;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param int $startDate
     * @param int $endDate
     * @param string $eventClassName
     * @return EventParser
     */
    public static function factory($contentObject, $startDate = 0, $endDate = 0, $eventClassName)
    {
        $type = $contentObject->package();
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
            $message[] = '<li>' . Translation :: get('TypeName', null, $type) . '</li>';
            $message[] = '</ul>';

            throw new ClassNotExistException($class);
        }
        return new $class($contentObject, $startDate, $endDate, $eventClassName);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        return $this->contentObject;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function setContentObject(ContentObject $contentObject)
    {
        $this->contentObject = $contentObject;
    }

    /**
     *
     * @return int
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @param int $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     *
     * @return int
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     *
     * @param int $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     *
     * @return string
     */
    public function getEventClassName()
    {
        return $this->eventClassName;
    }

    /**
     *
     * @param string $eventClassName
     */
    public function setEventClassName($eventClassName)
    {
        $this->eventClassName = $eventClassName;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event
     */
    public function getEventInstance()
    {
        $eventClassName = $this->getEventClassName();

        if (! $eventClassName)
        {
            throw new \Exception(
                'Please implement a local extension of the Event class in your context (' .
                     ClassnameUtilities :: getInstance()->getNamespaceFromClassname($eventClassName) . ')');
        }
        else
        {
            $event = new $eventClassName();

            if (! $event instanceof ContentObjectSupport)
            {
                throw new \Exception(
                    'Your event class (' . $eventClassName .
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
    abstract public function getEvents();
}
