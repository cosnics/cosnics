<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Form;

use Chamilo\Application\Weblcms\Service\Home\Connector;
use Chamilo\Application\Weblcms\Service\Home\FilteredCourseListBlockRenderer;
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
            FilteredCourseListBlockRenderer::CONFIGURATION_SHOW_NEW_ICONS,
            Translation::get('ShowNewIcons')
        );

        $connector = new Connector();

        $courseTypes = [];
        $courseTypes["-1"] = '-- ' . Translation::getInstance()->getTranslation('AllCourses') . ' --';
        $courseTypes = $courseTypes + $connector->get_course_types();

        $this->addElement(
            'select',
            FilteredCourseListBlockRenderer::CONFIGURATION_COURSE_TYPE,
            Translation::get('CourseType'),
            $courseTypes
        );
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $defaults = [];

        $defaults[FilteredCourseListBlockRenderer::CONFIGURATION_SHOW_NEW_ICONS] = $this->getBlock()->getSetting(
            FilteredCourseListBlockRenderer::CONFIGURATION_SHOW_NEW_ICONS,
            true
        );

        $defaults[FilteredCourseListBlockRenderer::CONFIGURATION_COURSE_TYPE] = $this->getBlock()->getSetting(
            FilteredCourseListBlockRenderer::CONFIGURATION_COURSE_TYPE,
            "-1"
        );

        parent::setDefaults($defaults, $filter);
    }
}