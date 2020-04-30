<?php
namespace Chamilo\Application\Weblcms\Trackers;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

/**
 *
 * @package application.lib.weblcms.trackers
 */
class WeblcmsPeerAssessmentPublicationSettingsTracker extends SimpleTracker
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE = 'direct_subscribe_available';
    const PROPERTY_UNSUBSCRIBE_AVAILABLE = 'unsubscribe_available';
    const PROPERTY_SUBSCRIPTION_DEADLINE = 'subscription_deadline';
    const PROPERTY_MIN_GROUP_MEMBERS = 'min_group_members';
    const PROPERTY_MAX_GROUP_MEMBERS = 'max_group_members';
    const PROPERTY_FILTER_MIN_MAX = 'filter_min_max';
    const PROPERTY_FILTER_SELF = 'filter_self';

    /**
     * Inherited
     * 
     * @see MainTracker :: track()
     */
    function validate_parameters(array $parameters = array())
    {
        $this->set_publication_id($parameters[self::PROPERTY_PUBLICATION_ID]);
        $this->set_direct_subscribe_available($parameters[self::PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE]);
        $this->set_unsubscribe_available($parameters[self::PROPERTY_UNSUBSCRIBE_AVAILABLE]);
        $this->set_subscription_deadline($parameters[self::PROPERTY_SUBSCRIPTION_DEADLINE]);
        $this->set_min_group_members($parameters[self::PROPERTY_MIN_GROUP_MEMBERS]);
        $this->set_max_group_members($parameters[self::PROPERTY_MAX_GROUP_MEMBERS]);
        $this->set_filter_min_max($parameters[self::PROPERTY_FILTER_MIN_MAX]);
        $this->set_filter_self($parameters[self::PROPERTY_FILTER_SELF]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PUBLICATION_ID, 
                self::PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE, 
                self::PROPERTY_UNSUBSCRIBE_AVAILABLE, 
                self::PROPERTY_SUBSCRIPTION_DEADLINE, 
                self::PROPERTY_MIN_GROUP_MEMBERS, 
                self::PROPERTY_MAX_GROUP_MEMBERS, 
                self::PROPERTY_FILTER_MIN_MAX, 
                self::PROPERTY_FILTER_SELF));
    }

    /*
     * function get_publication_id() { return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID); } function
     * set_publication_id($publication_id) { $this->set_default_property(self :: PROPERTY_PUBLICATION_ID,
     * $publication_id); }
     */
    function __call($name, array $arguments)
    {
        // generate error if no getter or setter is called and return
        if (! preg_match('/^(get|set)_(.+)$/', $name, $matches))
        {
            trigger_error('method not found', E_USER_ERROR);
            return;
        }
        // determine the method and property to be called
        $method = $matches[1] . '_default_property';
        $prop = constant(self::class . '::PROPERTY_' . strtoupper($matches[2]));
        // prepend the property to the argument list
        array_unshift($arguments, $prop);
        // call get_default_property or set_default_property with the arguments
        return call_user_func_array(array($this, $method), $arguments);
    }
}