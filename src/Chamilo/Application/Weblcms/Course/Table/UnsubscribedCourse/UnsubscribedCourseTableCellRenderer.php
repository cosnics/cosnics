<?php
namespace Chamilo\Application\Weblcms\Course\Table\UnsubscribedCourse;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Course\Table\CourseTable\CourseTableCellRenderer;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class describes the default cell renderer for the unsubscribed course table
 *
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class UnsubscribedCourseTableCellRenderer extends CourseTableCellRenderer
{

    // store user object to check view rights of a course
    private $user = null;

    private function can_access_course($course)
    {
        $user = $this->retrieve_user();
        $course_id = $course[Course::PROPERTY_ID];
        $courseObject = DataManager::retrieve_by_id(Course::class, $course_id);

        if ($this->is_teacher($course_id))
        {
            $allowed = true;
        }
        else
        {
            $course_settings_controller = CourseSettingsController::getInstance();
            $course_access = $course_settings_controller->get_course_setting(
                $courseObject, CourseSettingsConnector::COURSE_ACCESS
            );

            if ($course_access == CourseSettingsConnector::COURSE_ACCESS_CLOSED)
            {
                $allowed = false;
            }
            else
            {
                $open_course_access_type = $course_settings_controller->get_course_setting(
                    $courseObject, CourseSettingsConnector::OPEN_COURSE_ACCESS_TYPE
                );

                $is_subscribed = CourseDataManager::is_subscribed($course_id, $user);

                if ($is_subscribed || $open_course_access_type == CourseSettingsConnector::OPEN_COURSE_ACCESS_WORLD)
                {
                    $allowed = true;
                }
                else
                {
                    if ($open_course_access_type == CourseSettingsConnector::OPEN_COURSE_ACCESS_PLATFORM &&
                        !$user->is_anonymous_user())
                    {
                        $allowed = true;
                    }
                    else
                    {
                        $allowed = false;
                    }
                }
            }
        }

        return $allowed;
    }

    /**
     * Returns the actions toolbar
     *
     * @param $course Course
     *
     * @return String
     */
    public function get_actions($course)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if ($this->can_access_course($course))
        {

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewCourseHome'), new FontAwesomeGlyph('home'),
                    $this->get_component()->get_view_course_home_url($course[Course::PROPERTY_ID]),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (CourseManagementRights::getInstance()->is_allowed_management(
            CourseManagementRights::DIRECT_SUBSCRIBE_RIGHT, $course[Course::PROPERTY_ID]
        ))
        {

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Subscribe', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('plus-circle'),
                    $this->get_component()->get_subscribe_to_course_url($course[Course::PROPERTY_ID]),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }

    // duplicated from WeblcmsManager (application/weblcms/php/lib/weblcms_manager/weblcms_manager.class.php, line 1018)
    // to allow for passing direct course_id instead of Course orjbect.

    private function is_teacher($course_id)
    {
        $user = $this->retrieve_user();
        if ($user != null && $course_id != null)
        {
            $relation =
                $this->get_component()->get_parent()->retrieve_course_user_relation($course_id, $user->get_id());

            if (($relation && $relation->get_status() == 1) || $user->is_platform_admin())
            {
                return true;
            }
            else
            {
                return CourseDataManager::is_teacher_by_platform_group_subscription($course_id, $user);
            }
        }

        return false;
    }

    private function retrieve_user()
    {
        if ($this->user == null)
        {
            $this->user = $this->get_component()->get_user();
        }

        return $this->user;
    }
}
