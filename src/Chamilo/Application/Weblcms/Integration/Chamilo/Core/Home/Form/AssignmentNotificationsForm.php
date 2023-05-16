<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Form;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Connector;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\AssignmentNotifications;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\FilteredCourseList;
use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Translation\Translation;

/**
 * Configuration form for the AssignmentNotifications home block
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentNotificationsForm extends ConfigurationForm
{
    public function addSettings()
    {
        $connector = new Connector();

        $courseTypes = [];
        $courseTypes['-1'] = '-- ' . Translation::getInstance()->getTranslation('AllCourses') . ' --';
        $courseTypes = $courseTypes + $connector->get_course_types();

        $this->addElement(
            'select',
            AssignmentNotifications::CONFIGURATION_COURSE_TYPE,
            Translation::get('CourseType'),
            $courseTypes
        );
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $defaults[AssignmentNotifications::CONFIGURATION_COURSE_TYPE] = $this->getBlock()->getSetting(
            AssignmentNotifications::CONFIGURATION_COURSE_TYPE,
            '-1'
        );

        parent::setDefaults($defaults);
    }
}