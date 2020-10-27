<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package tracking.lib
 */

/**
 * This class presents a event
 *
 * @author Sven Vanpoucke
 */
abstract class Event extends DataClass
{
    const PROPERTY_ACTIVE = 'active';
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_NAME = 'name';

    private $trackers;

    public function run($parameters)
    {
        $parameters['event'] = $this->get_name();
        $data = array();

        $trackers = $this->get_trackers();
        foreach ($trackers as $tracker)
        {
            // FIXME: Temporary solution untill all trackers have been converted
            if (method_exists($tracker, 'set_event'))
            {
                $tracker->set_event($this);
            }

            $tracker->run($parameters);

            $data[] = $tracker;
        }

        return $data;
    }

    /**
     *
     * @param string $name The name of the event
     * @param string $application The name of the application
     *
     * @return Event The event
     */
    public static function eventFactory($name, $context)
    {
        $eventClass = $context . '\Event\\' . $name;

        return new $eventClass();
    }

    /**
     *
     * @return multitype:string
     */
    public function getTrackerClasses()
    {
        return array();
    }

    /**
     * Returns the active of this Event.
     *
     * @return the active.
     */
    public function get_active()
    {
        return $this->get_default_property(self::PROPERTY_ACTIVE);
    }

    /**
     * Returns the context of this Event.
     *
     * @return the context.
     */
    public function get_context()
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT);
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_NAME, self::PROPERTY_ACTIVE, self::PROPERTY_CONTEXT)
        );
    }

    /**
     * Returns the name of this Event.
     *
     * @return the name.
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'tracking_event';
    }

    public function get_trackers()
    {
        if (!$this->trackers)
        {
            $trackers = array();

            foreach ($this->getTrackerClasses() as $trackerClass)
            {
                $trackers[] = new $trackerClass();
            }

            $this->trackers = $trackers;
        }

        return $this->trackers;
    }

    public function set_trackers($trackers)
    {
        $this->trackers = $trackers;
    }

    /**
     *
     * @return boolean
     */
    public function is_active()
    {
        return $this->get_active() == true;
    }

    /**
     * Sets the active of this Event.
     *
     * @param active
     */
    public function set_active($active)
    {
        $this->set_default_property(self::PROPERTY_ACTIVE, $active);
    }

    /**
     * Sets the context of this Event.
     *
     * @param context
     */
    public function set_context($context)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Sets the name of this Event.
     *
     * @param name
     */
    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    public static function trigger($name, $context, $parameters)
    {
        $context .= '\Integration\Chamilo\Core\Tracking';

        return self::eventFactory($name, $context)->run($parameters);
    }
}
