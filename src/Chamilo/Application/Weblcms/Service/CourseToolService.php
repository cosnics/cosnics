<?php

namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\CourseSettingsService;


/**
 * Service class to manage weblcms courses.
 *
 * @package application\weblcms
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class CourseToolService
{
    /**
     * @var CourseService
     */
    protected $courseService;

    /**
     * @var CourseSettingsService
     */
    protected $courseSettingsService;

    /**
     * @param CourseService $courseService
     * @param CourseSettingsService $courseSettingsService
     */
    public function __construct(CourseService $courseService, CourseSettingsService $courseSettingsService)
    {
        $this->courseService = $courseService;
        $this->courseSettingsService = $courseSettingsService;
    }

    /**
     * @param Course|null $course
     * @param string $toolName
     *
     * @return bool
     */
    public function isCourseToolActive(?Course $course, string $toolName): bool
    {
        if (empty($course))
        {
            return false;
        }

        $toolRegistrationId = $this->courseService->getToolRegistration($toolName)->getId();
        if (!$toolRegistrationId)
        {
            return false;
        }

        return $this->courseSettingsService->isToolActive($course, $toolRegistrationId);
    }
}
