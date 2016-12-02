<?php
namespace Chamilo\Application\Weblcms\Service\Interfaces;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;

/**
 * Interface for the CourseSettingsService
 * 
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseSettingsServiceInterface
{

    /**
     * Checks if the given course is visible
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseVisible(Course $course);

    /**
     * Checks if the given course is set to open
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseOpen(Course $course);

    /**
     * Checks if the given course is open for the world
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseOpenForWorld(Course $course);

    /**
     * Checks if the given course is open for the platform
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseOpenForPlatform(Course $course);

    /**
     * Checks if the given course is open for the registered users
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isCourseOpenForRegisteredUsers(Course $course);

    /**
     * Checks if the given tool is active
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $toolRegistrationId
     *
     * @return bool
     */
    public function isToolActive(Course $course, $toolRegistrationId);

    /**
     * Checks if the given tool is active
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $toolRegistrationId
     *
     * @return bool
     */
    public function isToolVisible(Course $course, $toolRegistrationId);
}