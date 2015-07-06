<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;

/**
 *
 * @package Chamilo\Application\Weblcms\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsService
{

    /**
     *
     * @var \Chamilo\Application\Weblcms\Service\RightsService
     */
    private static $instance;

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string[] $publication
     * @return boolean
     */
    public function canEditPublicationContentObject(User $user, Course $course, $publication)
    {
        return $this->isTeacher($user, $course) && $this->isCollaborationAllowed($publication);
    }

    /**
     *
     * @param string[] $publication $publication
     * @return boolean
     */
    public function isCollaborationAllowed($publication)
    {
        return $publication[ContentObjectPublication :: PROPERTY_ALLOW_COLLABORATION];
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @return boolean
     */
    public function isTeacher(User $user, Course $course)
    {
        if ($user->is_platform_admin())
        {
            return true;
        }

        $courseUserRelation = CourseDataManager :: retrieve_course_user_relation_by_course_and_user(
            $course->getId(),
            $user->getId());

        if ($courseUserRelation instanceof CourseUserRelation &&
             $courseUserRelation->get_status() == CourseUserRelation :: STATUS_TEACHER)
        {
            return true;
        }
        else
        {
            return CourseDataManager :: is_teacher_by_platform_group_subscription($course->get_id(), $user);
        }
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Service\RightsService
     */
    static public function getInstance()
    {
        if (is_null(static :: $instance))
        {
            self :: $instance = new static();
        }

        return static :: $instance;
    }
}