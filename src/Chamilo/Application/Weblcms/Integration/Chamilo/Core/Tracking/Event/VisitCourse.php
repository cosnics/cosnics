<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Event;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Core\Tracking\Storage\DataClass\Event;

/**
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VisitCourse extends Event
{

    /**
     *
     * @see \Chamilo\Core\Tracking\Storage\DataClass\Event::getTrackerClasses()
     */
    public function getTrackerClasses()
    {
        return array(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit::class_name());
    }

    public function getType()
    {
        return CourseVisit::TYPE_VISIT;
    }
}