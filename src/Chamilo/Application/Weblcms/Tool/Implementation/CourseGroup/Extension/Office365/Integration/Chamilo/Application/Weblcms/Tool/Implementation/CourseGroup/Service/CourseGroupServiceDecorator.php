<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupFormDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Decorates the service for course groups. Adding additional functionality for the common course group functionality
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupServiceDecorator implements CourseGroupServiceDecoratorInterface
{
    /**
     * Decorates the create functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param array $formValues
     */
    public function createGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        if($this->usesPlanner($formValues))
        {
            // check for group in o365
            // create group if not exists
            // subscribe user to group
            // subscribe all teachers to group
            // check for plan for group in o365
            // create plan if not exists
            // store plan / group name / reference?
        }
    }

    /**
     * Decorates the update functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param array $formValues
     */
    public function updateGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        if($this->usesPlanner($formValues))
        {
            // check for group in o365
            // create group if not exists
            // subscribe user to group
            // subscribe all teachers to group
            // subscribe all students to group
            // check for plan for group in o365
            // create plan if not exists
            // store plan / group name / reference?
        }
    }

    /**
     * Decorates the delete functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     */
    public function deleteGroup(CourseGroup $courseGroup)
    {
        // check for group in o365
        // remove all students from group
        // remove reference to plan
    }

    /**
     * Decorates the subscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUser(CourseGroup $courseGroup, User $user)
    {
        // check if o-group / plan is active for group
        // subscribe user to group
    }

    /**
     * Decorates the unsubscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unsubscribeUser(CourseGroup $courseGroup, User $user)
    {
        // check if o-group / plan is active for group
        // unsubscribe user from group
    }

    /**
     * @param array $formValues
     *
     * @return bool
     */
    protected function usesPlanner($formValues = [])
    {
        return boolval($formValues[CourseGroupFormDecorator::PROPERTY_USE_PLANNER]);
    }
}