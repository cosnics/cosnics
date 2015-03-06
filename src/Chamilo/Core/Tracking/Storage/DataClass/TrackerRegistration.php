<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * $Id: tracker_registration.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * 
 * @package tracking.lib
 */

/**
 * This class presents a tracker registration
 * 
 * @author Sven Vanpoucke
 */
class TrackerRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * Tracker properties
     */
    const PROPERTY_TRACKER = 'tracker';
    const PROPERTY_CONTEXT = 'context';

    /**
     * Get the default properties
     * 
     * @return array The property names.
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TRACKER, self :: PROPERTY_CONTEXT));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     * Returns the class of this Tracker.
     * 
     * @return the class.
     */
    public function get_tracker()
    {
        return $this->get_default_property(self :: PROPERTY_TRACKER);
    }

    /**
     * Sets the class of this Tracker.
     * 
     * @param class
     */
    public function set_tracker($tracker)
    {
        $this->set_default_property(self :: PROPERTY_TRACKER, $tracker);
    }

    /**
     * Returns the context of this Tracker.
     * 
     * @return string The context.
     */
    public function get_context()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT);
    }

    /**
     * Sets the context of this Tracker.
     * 
     * @param string The context
     */
    public function set_context($context)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT, $context);
    }

    /**
     * Returns the activity of this tracker registration for an event
     * 
     * @return bool active
     */
    public function get_active()
    {
        return $this->active;
    }

    /**
     * Sets the activity of this tracker registration for an event
     * 
     * @param bool active
     */
    public function set_active($active)
    {
        $this->active = $active;
    }
}
