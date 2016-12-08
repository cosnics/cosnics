<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseSettingsServiceInterface;

/**
 * Service wrapper for the course settings controller
 * 
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseSettingsService implements CourseSettingsServiceInterface
{

    /**
     * The course settings controller
     * 
     * @var CourseSettingsController
     */
    private $courseSettingsController;

    /**
     * Constructor
     * 
     * @param CourseSettingsController $courseSettingsController
     */
    public function __construct(CourseSettingsController $courseSettingsController)
    {
        $this->courseSettingsController = $courseSettingsController;
    }

    /**
     * Checks if the given course is visible
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseVisible(Course $course)
    {
        return (bool) $this->courseSettingsController->get_course_setting($course, CourseSettingsConnector::VISIBILITY);
    }

    /**
     * Checks if the given course is set to open
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseOpen(Course $course)
    {
        $courseAccess = $this->courseSettingsController->get_course_setting(
            $course, 
            CourseSettingsConnector::COURSE_ACCESS);
        
        return $courseAccess == CourseSettingsConnector::COURSE_ACCESS_OPEN;
    }

    /**
     * Checks if the given course is open for the world
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseOpenForWorld(Course $course)
    {
        return $this->getCourseAccessType($course) == CourseSettingsConnector::OPEN_COURSE_ACCESS_WORLD;
    }

    /**
     * Checks if the given course is open for the platform
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseOpenForPlatform(Course $course)
    {
        return $this->getCourseAccessType($course) == CourseSettingsConnector::OPEN_COURSE_ACCESS_PLATFORM;
    }

    /**
     * Checks if the given course is open for the registered users
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseOpenForRegisteredUsers(Course $course)
    {
        return $this->getCourseAccessType($course) == CourseSettingsConnector::OPEN_COURSE_ACCESS_REGISTERED_USERS;
    }

    /**
     * Checks if the given course is set to closed
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseClosed(Course $course)
    {
        $courseAccess = $this->courseSettingsController->get_course_setting(
            $course, 
            CourseSettingsConnector::COURSE_ACCESS);
        
        return $courseAccess == CourseSettingsConnector::COURSE_ACCESS_CLOSED;
    }

    /**
     * Checks if the given tool is active
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $toolRegistrationId
     *
     * @return bool
     */
    public function isToolActive(Course $course, $toolRegistrationId)
    {
        return $this->courseSettingsController->get_course_setting(
            $course, 
            CourseSetting::COURSE_SETTING_TOOL_ACTIVE, 
            $toolRegistrationId);
    }

    /**
     * Checks if the given tool is active
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $toolRegistrationId
     *
     * @return bool
     */
    public function isToolVisible(Course $course, $toolRegistrationId)
    {
        return $this->courseSettingsController->get_course_setting(
            $course, 
            CourseSetting::COURSE_SETTING_TOOL_VISIBLE, 
            $toolRegistrationId);
    }

    /**
     * Retrieves the course access type from the course settings
     * 
     * @param Course $course
     *
     * @return int
     */
    protected function getCourseAccessType(Course $course)
    {
        return $this->courseSettingsController->get_course_setting(
            $course, 
            CourseSettingsConnector::OPEN_COURSE_ACCESS_TYPE);
    }
}