<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupFormDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\TeamNotFoundException;

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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    public function createGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        $useTeam = $formValues[CourseGroupFormDecorator::PROPERTY_USE_TEAM][0];
        $this->createTeamByChoice($courseGroup, $user, $useTeam);
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $user
     * @param int $useTeamENUM
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    protected function createTeamByChoice(CourseGroup $courseGroup, User $user, $useTeamENUM)
    {
        if($useTeamENUM == CourseGroupFormDecorator::OPTION_REGULAR_TEAM)
        {
            $this->courseGroupOffice365Connector->createStandardTeamFromCourseGroup($courseGroup, $user);
        }
        if($useTeamENUM == CourseGroupFormDecorator::OPTION_CLASS_TEAM)
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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    public function updateGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        $useTeamFormValue = $formValues[CourseGroupFormDecorator::PROPERTY_USE_TEAM][$courseGroup->getId()];

        if($this->courseGroupOffice365Connector->courseGroupHasTeam($courseGroup))
        {
            if(!boolval($useTeamFormValue))
            {
                $this->courseGroupOffice365Connector->removeTeamFromCourseGroup($courseGroup, $user);
            }
            else
            {
                try
                {
                    $this->courseGroupOffice365Connector->updateTeamNameFromCourseGroup($courseGroup);
                }
                catch(\Exception $ex) {}
            }
        }
        else
        {
            $this->createTeamByChoice($courseGroup, $user, $useTeamFormValue);
        }
    }

    /**
     * Decorates the delete functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function deleteGroup(CourseGroup $courseGroup, User $user)
    {
        $this->courseGroupOffice365Connector->removeTeamFromCourseGroup($courseGroup, $user);
    }

    /**
     * Decorates the subscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    public function subscribeUser(CourseGroup $courseGroup, User $user)
    {
        try
        {
            $this->courseGroupOffice365Connector->subscribeUser($courseGroup, $user);
        }
        catch(TeamNotFoundException $ex) {}
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
        try
        {
            $this->courseGroupOffice365Connector->unsubscribeUser($courseGroup, $user);
        }
        catch(TeamNotFoundException $ex) {}
    }

}
