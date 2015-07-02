<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;

class CourseChange extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;
    
    // Can be used for subscribsion of users / classes
    const PROPERTY_TARGET_REFERENCE_ID = 'target_reference_id';

    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TARGET_REFERENCE_ID));
    }

    public function validate_parameters(array $parameters = array())
    {
        parent :: validate_parameters($parameters);
        
        if ($parameters[self :: PROPERTY_TARGET_REFERENCE_ID])
        {
            $this->set_target_reference_id($parameters[self :: PROPERTY_TARGET_REFERENCE_ID]);
        }
        else
        {
            $this->set_target_reference_id(0);
        }
    }

    public function get_target_reference_id()
    {
        return $this->get_default_property(self :: PROPERTY_TARGET_REFERENCE_ID);
    }

    public function set_target_reference_id($target_reference_id)
    {
        $this->set_default_property(self :: PROPERTY_TARGET_REFERENCE_ID, $target_reference_id);
    }
}
