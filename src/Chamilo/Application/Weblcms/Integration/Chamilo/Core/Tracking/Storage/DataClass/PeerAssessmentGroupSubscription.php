<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

class PeerAssessmentGroupSubscription extends \Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_GROUP_ID = 'group_id';

    public function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_group_id($parameters[self :: PROPERTY_GROUP_ID]);
    }

    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_GROUP_ID));
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
        $prop = constant($this :: CLASS_NAME . '::PROPERTY_' . strtoupper($matches[2]));
        // prepend the property to the argument list
        array_unshift($arguments, $prop);
        // call get_default_property or set_default_property with the arguments
        return call_user_func_array(array($this, $method), $arguments);
    }
}
