<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplayItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 * $Id: complex_survey.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.survey
 */
class ComplexSurvey extends ComplexContentObjectItem implements SurveyDisplayItem
{
    const PROPERTY_VISIBLE = 'visible';
    
    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_VISIBLE);
    }
    
    function get_visible()
    {
        return $this->get_additional_property(self :: PROPERTY_VISIBLE);
    }
    
    function set_visible($value)
    {
        $this->set_additional_property(self :: PROPERTY_VISIBLE, $value);
    }
    
    function is_visible()
    {
        return $this->get_visible() == 1;
    }
    
    function toggle_visibility()
    {
        $this->set_visible(! $this->get_visible());
    }
    
    function getDataAttributes()
    {
        return null;
    }
    
}
?>