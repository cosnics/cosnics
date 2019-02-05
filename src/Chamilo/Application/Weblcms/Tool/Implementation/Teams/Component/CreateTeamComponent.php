<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\CourseTeamAlreadyExistsException;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class CreateTeamComponent
 */
class CreateTeamComponent extends Manager
{

    use ContainerAwareTrait;

    /**
     * @return string|void
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function run()
    {
        //only teachers can create the team
        if(!$this->get_course()->is_course_admin($this->getUser())) {
            throw new NotAllowedException();
        }

        /**
         * @var CourseTeamService $courseTeamService
         */
        $courseTeamService = $this->getService(CourseTeamService::class);

        $isError = false;

        try {
            $courseTeamService->createTeam($this->getUser(), $this->get_course());
            $message = $this->getTranslator()->trans('TeamCreated', [], Manager::class);
        } catch (CourseTeamAlreadyExistsException $exception) { //race conditions
                $message = $exception->getMessage();
                $isError = true;
        }

        $browserUrlParameters = $this->get_parameters();
        $browserUrlParameters[self::PARAM_ACTION]= self::ACTION_BROWSE;

        $this->redirect($message, $isError, $browserUrlParameters);
    }
}