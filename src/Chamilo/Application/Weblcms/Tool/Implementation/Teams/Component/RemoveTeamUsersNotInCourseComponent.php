<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class CreateTeamComponent
 */
class RemoveTeamUsersNotInCourseComponent extends Manager
{

    use ContainerAwareTrait;

    /**
     * @return string|void
     * @throws NotAllowedException
     * @throws GraphException
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
        $courseTeamService->removeTeamUsersNotInCourse($this->get_course());

        $message = $this->getTranslator()->trans('TeamUsersSynced', [], Manager::class);

        $browserUrlParameters = $this->get_parameters();
        $browserUrlParameters[self::PARAM_ACTION]= self::ACTION_BROWSE;

        $this->redirect($message, false, $browserUrlParameters);
    }
}