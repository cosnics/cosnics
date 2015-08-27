<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;

/**
 * Tracks the time when a course is left
 *
 * @author Sven Vanpoucke
 * @package application.weblcms
 */
class LeaveCourseComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{
    const PARAM_COURSE_VISIT_TRACKER_ID = 'course_visit_tracker_id';

    /**
     * Run the AJAX component
     */
    public function run()
    {
        Event :: trigger(
            'LeaveCourse',
            Manager :: context(),
            array(CourseVisit :: PROPERTY_ID => $this->get_parameter(self :: PARAM_COURSE_VISIT_TRACKER_ID)));
    }

    /**
     * Get an array of parameters which should be set for this call to work
     *
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_COURSE_VISIT_TRACKER_ID);
    }
}