<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DeleteCourseTeamComponent extends Manager
{
    /**
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        if (!$this->get_course()->is_course_admin($this->getUser()))
        {
            throw new NotAllowedException();
        }

        try
        {
            $this->getCourseTeamService()->removeTeam($this->get_course());
            $message = 'TeamRemoved';
            $success = true;
        }
        catch (UserException $ex)
        {
            throw $ex;
        }
        catch (\Exception $ex)
        {
            $message = 'TeamNotRemoved';
            $this->getExceptionLogger()->logException($ex);
            $success = false;
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()),
            !$success, [self::PARAM_ACTION => self::ACTION_BROWSE]
        );

        return;
    }
}
