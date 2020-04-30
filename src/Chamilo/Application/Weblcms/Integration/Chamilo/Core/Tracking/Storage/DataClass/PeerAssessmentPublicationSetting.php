<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

class PeerAssessmentPublicationSetting extends SimpleTracker
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE = 'direct_subscribe_available';
    const PROPERTY_UNSUBSCRIBE_AVAILABLE = 'unsubscribe_available';
    const PROPERTY_SUBSCRIPTION_DEADLINE = 'subscription_deadline';
    const PROPERTY_MIN_GROUP_MEMBERS = 'min_group_members';
    const PROPERTY_MAX_GROUP_MEMBERS = 'max_group_members';
    const PROPERTY_FILTER_MIN_MAX = 'filter_min_max';
    const PROPERTY_FILTER_SELF = 'filter_self';
    const PROPERTY_ANONYMOUS_FEEDBACK = 'anonymous_feedback';
    const PROPERTY_ENABLE_USER_RESULTS_EXPORT = 'enable_user_results_export';

    public function validate_parameters(array $parameters = array())
    {
        $this->set_publication_id($parameters[self::PROPERTY_PUBLICATION_ID]);
        $this->set_direct_subscribe_available($parameters[self::PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE]);
        $this->set_unsubscribe_available($parameters[self::PROPERTY_UNSUBSCRIBE_AVAILABLE]);
        $this->set_subscription_deadline(
            DatetimeUtilities::time_from_datepicker_without_timepicker(
                $parameters[self::PROPERTY_SUBSCRIPTION_DEADLINE]));
        $this->set_min_group_members($parameters[self::PROPERTY_MIN_GROUP_MEMBERS]);
        $this->set_max_group_members($parameters[self::PROPERTY_MAX_GROUP_MEMBERS]);
        $this->set_filter_min_max($parameters[self::PROPERTY_FILTER_MIN_MAX]);
        $this->set_filter_self($parameters[self::PROPERTY_FILTER_SELF]);
        $this->set_anonymous_feedback($parameters[self::PROPERTY_ANONYMOUS_FEEDBACK]);
        $this->set_enable_user_results_export($parameters[self::PROPERTY_ENABLE_USER_RESULTS_EXPORT]);
    }

    public static function get_default_property_names($extended_property_names = array())
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
                self::PROPERTY_FILTER_SELF, 
                self::PROPERTY_FILTER_MIN_MAX, 
                self::PROPERTY_ANONYMOUS_FEEDBACK, 
                self::PROPERTY_FILTER_MIN_MAX, 
                self::PROPERTY_ENABLE_USER_RESULTS_EXPORT));
    }

    public function __call($name, array $arguments)
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
