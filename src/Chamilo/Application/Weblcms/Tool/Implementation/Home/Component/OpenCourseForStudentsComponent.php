<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Home\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseForStudentsComponent extends Manager
{
    public function run()
    {
        if (!$this->get_parent()->is_teacher() && !$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        try
        {
            $courseSettingsController = CourseSettingsController::getInstance();

            $settings = [
                CourseSettingsController::SETTING_PARAM_COURSE_SETTINGS => [
                    CourseSettingsConnector::COURSE_ACCESS => CourseSettingsConnector::COURSE_ACCESS_OPEN,
                    CourseSettingsConnector::VISIBILITY => 1
                ]
            ];

            if(
                !$courseSettingsController->handle_settings_for_object_with_given_values(
                    $this->get_course(), $settings, CourseSettingsController::SETTING_ACTION_UPDATE
                )
            )
            {
                throw new \RuntimeException('Failed to update settings');
            }

            $success = true;
            $message = 'CourseOpenedForStudents';
        }
        catch(\Exception $ex)
        {
            $success = false;
            $message = 'CourseNotOpenedForStudents';

            $this->getExceptionLogger()->logException($ex);
        }

        $this->redirect($message, !$success, [self::PARAM_ACTION => self::ACTION_BROWSE]);

    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}