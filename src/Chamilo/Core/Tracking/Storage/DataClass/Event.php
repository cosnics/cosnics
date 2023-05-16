<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Tracking\Storage\DataClass
 * @author  Sven Vanpoucke
 */
abstract class Event extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ACTIVE = 'active';
    public const PROPERTY_CONTEXT = 'context';
    public const PROPERTY_NAME = 'name';

    private $trackers;

    public function run($parameters)
    {
        $parameters['event'] = $this->get_name();
        $data = [];

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
     * @param string $name        The name of the event
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
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_NAME, self::PROPERTY_ACTIVE, self::PROPERTY_CONTEXT]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_event';
    }

    /**
     * @return string[]
     */
    public function getTrackerClasses()
    {
        return [];
    }

    /**
     * Returns the active of this Event.
     *
     * @return the active.
     */
    public function get_active()
    {
        return $this->getDefaultProperty(self::PROPERTY_ACTIVE);
    }

    /**
     * Returns the context of this Event.
     *
     * @return the context.
     */
    public function get_context()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    /**
     * Returns the name of this Event.
     *
     * @return the name.
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    public function get_trackers()
    {
        if (!$this->trackers)
        {
            $trackers = [];

            foreach ($this->getTrackerClasses() as $trackerClass)
            {
                $trackers[] = new $trackerClass();
            }

            $this->trackers = $trackers;
        }

        return $this->trackers;
    }

    /**
     * @return bool
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
        $this->setDefaultProperty(self::PROPERTY_ACTIVE, $active);
    }

    /**
     * Sets the context of this Event.
     *
     * @param context
     */
    public function set_context($context)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Sets the name of this Event.
     *
     * @param name
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    public function set_trackers($trackers)
    {
        $this->trackers = $trackers;
    }

    public static function trigger($name, $context, $parameters)
    {
        $context .= '\Integration\Chamilo\Core\Tracking';

        return self::eventFactory($name, $context)->run($parameters);
    }
}
