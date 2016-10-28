<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

class PeerAssessmentAttempt extends \Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_START_DATE = 'start_date';
    const PROPERTY_END_DATE = 'end_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_WEIGHT = 'weight';

    public function validate_parameters(array $parameters = array())
    {
        $this->set_publication_id($parameters[self :: PROPERTY_PUBLICATION_ID]);
        $this->set_title($parameters[self :: PROPERTY_TITLE]);
        $this->set_description($parameters[self :: PROPERTY_DESCRIPTION]);
        $this->set_start_date(strtotime($parameters[self :: PROPERTY_START_DATE]));
        $this->set_end_date(strtotime($parameters[self :: PROPERTY_END_DATE]));
        $this->set_hidden($parameters[self :: PROPERTY_HIDDEN]);
        $this->set_weight($parameters[self :: PROPERTY_WEIGHT]);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_PUBLICATION_ID,
                self :: PROPERTY_TITLE,
                self :: PROPERTY_DESCRIPTION,
                self :: PROPERTY_START_DATE,
                self :: PROPERTY_END_DATE,
                self :: PROPERTY_HIDDEN,
                self :: PROPERTY_WEIGHT));
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
        $prop = constant($this :: class_name() . '::PROPERTY_' . strtoupper($matches[2]));
        // prepend the property to the argument list
        array_unshift($arguments, $prop);
        // call get_default_property or set_default_property with the arguments
        return call_user_func_array(array($this, $method), $arguments);
    }
}
