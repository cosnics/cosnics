<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\CourseTeamAlreadyExistsException;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\CourseTeamNotExistsException;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class GoToTeamComponent
 */
class GoToTeamComponent extends Manager
{

    use ContainerAwareTrait;

    /**
     * @return string|RedirectResponse
     * @throws CourseTeamNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function run()
    {
        /**
         * @var CourseTeamService $courseTeamService
         */
        $courseTeamService = $this->getService(CourseTeamService::class);
        $team = $courseTeamService->getTeam($this->get_course());

        if(is_null($team)) {
            throw new CourseTeamNotExistsException($this->get_course());
        }

        /**
         * @var TeamService $teamService
         */
        $teamService = $this->getService(TeamService::class);

        if($this->get_course()->is_course_admin($this->getUser())) {
            $teamService->addOwner($this->getUser(), $team);
        } else {
            $teamService->addMember($this->getUser(), $team);
        }

        return new RedirectResponse($team->getWebUrl());
    }
}