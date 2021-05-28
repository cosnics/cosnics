<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Form;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Connector;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\FilteredCourseList;
use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Translation\Translation;

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
            Translation::get('ShowNewIcons')
        );

        $connector = new Connector();

        $courseTypes = [];
        $courseTypes["-1"] = '-- ' . Translation::getInstance()->getTranslation('AllCourses') . ' --';
        $courseTypes = $courseTypes + $connector->get_course_types();

        $this->addElement(
            'select',
            FilteredCourseList::CONFIGURATION_COURSE_TYPE,
            Translation::get('CourseType'),
            $courseTypes
        );
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $defaults = array();

        $defaults[FilteredCourseList::CONFIGURATION_SHOW_NEW_ICONS] = $this->getBlock()->getSetting(
            FilteredCourseList::CONFIGURATION_SHOW_NEW_ICONS,
            true
        );

        $defaults[FilteredCourseList::CONFIGURATION_COURSE_TYPE] = $this->getBlock()->getSetting(
            FilteredCourseList::CONFIGURATION_COURSE_TYPE,
            "-1"
        );

        parent::setDefaults($defaults, $filter);
    }
}