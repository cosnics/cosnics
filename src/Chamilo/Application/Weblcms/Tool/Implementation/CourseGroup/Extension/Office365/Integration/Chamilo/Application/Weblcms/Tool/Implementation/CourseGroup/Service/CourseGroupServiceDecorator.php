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
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365Connector
     */
    protected $courseGroupOffice365Connector;

    /**
     * CourseGroupServiceDecorator constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365Connector $courseGroupOffice365Connector
     */
    public function __construct(CourseGroupOffice365Connector $courseGroupOffice365Connector)
    {
        $this->courseGroupOffice365Connector = $courseGroupOffice365Connector;
    }

    /**
     * Decorates the create functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param array $formValues
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException
     */
    public function createGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        $useTeam = $formValues[CourseGroupFormDecorator::PROPERTY_USE_TEAM];

        if($useTeam == CourseGroupFormDecorator::OPTION_REGULAR_TEAM)
        {
            $this->courseGroupOffice365Connector->createGroupAndTeamFromCourseGroup($courseGroup, $user);
        }
        if($useTeam == CourseGroupFormDecorator::OPTION_CLASS_TEAM)
        {
            $this->courseGroupOffice365Connector->createClassTeamFromCourseGroup($courseGroup, $user);
        }
    }

    /**
     * Decorates the update functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param array $formValues
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException
     */
    public function updateGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        if($this->courseGroupOffice365Connector->courseGroupHasTeam($courseGroup))
        {
            if(!boolval($formValues[CourseGroupFormDecorator::PROPERTY_USE_TEAM]))
            {
                $this->courseGroupOffice365Connector->unlinkTeamFromOffice365Group($courseGroup, $user);
            }
        }
        else
        {
            $this->createGroup($courseGroup, $user, $formValues);
        }
    }

    /**
     * Decorates the delete functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function deleteGroup(CourseGroup $courseGroup, User $user)
    {
        $this->courseGroupOffice365Connector->unlinkOffice365GroupFromCourseGroup($courseGroup, $user);
    }

    /**
     * Decorates the subscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function subscribeUser(CourseGroup $courseGroup, User $user)
    {
        $this->courseGroupOffice365Connector->subscribeUser($courseGroup, $user);
    }

    /**
     * Decorates the unsubscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function unsubscribeUser(CourseGroup $courseGroup, User $user)
    {
        $this->courseGroupOffice365Connector->unsubscribeUser($courseGroup, $user);
    }

    /**
     * @param array $formValues
     *
     * @return bool
     */
    protected function usesTeam($formValues = [])
    {
        return boolval($formValues[CourseGroupFormDecorator::PROPERTY_USE_TEAM]);
    }
}
