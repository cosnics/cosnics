<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\CourseTypeCourseList;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Connector;

class CourseTypeCourseListForm extends ConfigurationForm
{

    /**
     *
     * @see \Chamilo\Core\Home\Form\ConfigurationForm::addSettings()
     */
    public function addSettings()
    {
        $this->addElement(
            'checkbox',
            CourseTypeCourseList :: CONFIGURATION_SHOW_WHEN_EMPTY,
            Translation :: get('ShowWhenEmpty'));

        $this->addElement(
            'checkbox',
            CourseTypeCourseList :: CONFIGURATION_SHOW_NEW_ICONS,
            Translation :: get('ShowNewIcons'));

        $connector = new Connector();

        $this->addElement(
            'select',
            CourseTypeCourseList :: CONFIGURATION_COURSE_TYPE,
            Translation :: get('CourseType'),
            $connector->get_all_course_types());
    }

    public function setDefaults()
    {
        $defaults = array();

        $defaults[CourseTypeCourseList :: CONFIGURATION_SHOW_WHEN_EMPTY] = $this->getBlock()->getSetting(
            CourseTypeCourseList :: CONFIGURATION_SHOW_WHEN_EMPTY,
            true);
        $defaults[CourseTypeCourseList :: CONFIGURATION_SHOW_NEW_ICONS] = $this->getBlock()->getSetting(
            CourseTypeCourseList :: CONFIGURATION_SHOW_NEW_ICONS,
            true);
        $defaults[CourseTypeCourseList :: CONFIGURATION_COURSE_TYPE] = $this->getBlock()->getSetting(
            CourseTypeCourseList :: CONFIGURATION_COURSE_TYPE,
            0);

        parent :: setDefaults($defaults);
    }
}