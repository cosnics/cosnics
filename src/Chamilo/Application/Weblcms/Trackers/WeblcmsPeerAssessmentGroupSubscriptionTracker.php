<?php
namespace Chamilo\Application\Weblcms\Trackers;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

/**
 *
 * @package application.lib.weblcms.trackers
 */
class WeblcmsPeerAssessmentGroupSubscriptionTracker extends SimpleTracker
{
    const PROPERTY_GROUP_ID = 'group_id';

    const PROPERTY_USER_ID = 'user_id';

    function __call($name, array $arguments)
    {
        // generate error if no getter or setter is called and return
        if (!preg_match('/^(get|set)_(.+)$/', $name, $matches))
        {
            trigger_error('method not found', E_USER_ERROR);

            return;
        }
        // determine the method and property to be called
        $method = $matches[1] . '_default_property';
        $prop = constant(static::class . '::PROPERTY_' . strtoupper($matches[2]));
        // prepend the property to the argument list
        array_unshift($arguments, $prop);

        // call get_default_property or set_default_property with the arguments
        return call_user_func_array(array($this, $method), $arguments);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent::get_default_property_names(array(self::PROPERTY_USER_ID, self::PROPERTY_GROUP_ID));
    }

    /*
     * function get_user_id() { return $this->get_default_property(self :: PROPERTY_USER_ID); } function
     * set_user_id($user_id) { $this->set_default_property(self :: PROPERTY_USER_ID, $user_id); } function
     * get_group_id() { return $this->get_default_property(self :: PROPERTY_GROUP_ID); } function
     * set_group_id($group_id) { $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id); }
     */

    /**
     * Inherited
     *
     * @see MainTracker :: track()
     */
    function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_group_id($parameters[self::PROPERTY_GROUP_ID]);
    }
}