<?php
namespace Chamilo\Application\Weblcms\CourseType\Form;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Form\CommonCourseForm;
use Chamilo\Application\Weblcms\Interfaces\FormLockedSettingsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes a form for the course type object
 * 
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeForm extends CommonCourseForm implements FormLockedSettingsSupport
{
    const PROPERTY_FORCE_UPDATE = 'force_update';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Builds the elements for the general tab
     */
    public function build_general_tab_form_elements()
    {
        $this->add_textfield(CourseType :: PROPERTY_TITLE, Translation :: get('CourseTypeTitle'));
        $this->add_html_editor(CourseType :: PROPERTY_DESCRIPTION, Translation :: get('CourseTypeDescription'), false);
        $this->addElement(
            'checkbox', 
            CourseType :: PROPERTY_ACTIVE, 
            Translation :: get('CourseTypeActive'), 
            '', 
            null, 
            '1', 
            '0');
        
        // if ($this->get_base_object()->is_identified())
        // {
        // $this->addElement('html', '<div class="warning-message">'
        // . Translation :: get('ForceUpdateWarning') . '</div>');
        // $this->addElement('checkbox', self :: PROPERTY_FORCE_UPDATE, Translation :: get('ForceUpdate'));
        // }
    }

    /**
     * Returns the defaults for the selected base object (course_type)
     * 
     * @param $base_object DataClass
     *
     * @return string[]
     */
    public function get_base_object_default_values(DataClass $base_object)
    {
        $default_values = array();
        
        $default_values[CourseType :: PROPERTY_TITLE] = $base_object->get_title();
        $default_values[CourseType :: PROPERTY_DESCRIPTION] = $base_object->get_description();
        $default_values[CourseType :: PROPERTY_ACTIVE] = $base_object->is_active();
        
        return $default_values;
    }
}
