<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
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
     * @throws UserException
     */
    public function run()
    {
        //only teachers can create the team
        if (!$this->get_course()->is_course_admin($this->getUser()))
        {
            throw new NotAllowedException();
        }

        try
        {
            $this->getCourseTeamService()->removeTeamUsersNotInCourse($this->get_course());

            $message = 'TeamUsersSynced';
            $success = true;
        }
        catch (UserException $ex)
        {
            throw $ex;
        }
        catch (\Exception $ex)
        {
            $message = 'TeamUsersNotSynced';
            $success = false;
            $this->getExceptionLogger()->logException($ex);
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()), !$success,
            [self::PARAM_ACTION => self::ACTION_BROWSE], [self::PARAM_PLATFORM_GROUP_TEAM_ID]
        );
    }
}
