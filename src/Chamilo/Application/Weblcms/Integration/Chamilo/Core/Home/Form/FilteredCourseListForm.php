<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Form;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Connector;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\FilteredCourseList;
use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Platform\Translation;

class FilteredCourseListForm extends ConfigurationForm
{

    /**
     *
     * @see \Chamilo\Core\Home\Form\ConfigurationForm::addSettings()
     */
    public function addSettings()
    {
        $this->addElement(
            'checkbox', 
            FilteredCourseList::CONFIGURATION_SHOW_NEW_ICONS, 
            Translation::get('ShowNewIcons'));
        
        $connector = new Connector();
        
        $this->addElement(
            'select', 
            FilteredCourseList::CONFIGURATION_COURSE_TYPE, 
            Translation::get('CourseType'), 
            $connector->get_course_types());
    }

    public function setDefaults()
    {
        $defaults = array();
        
        $defaults[FilteredCourseList::CONFIGURATION_SHOW_NEW_ICONS] = $this->getBlock()->getSetting(
            FilteredCourseList::CONFIGURATION_SHOW_NEW_ICONS, 
            true);
        $defaults[FilteredCourseList::CONFIGURATION_COURSE_TYPE] = $this->getBlock()->getSetting(
            FilteredCourseList::CONFIGURATION_COURSE_TYPE, 
            0);
        
        parent::setDefaults($defaults);
    }
}