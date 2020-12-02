<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ListUsersComponent extends Manager
{
    /**
     * @return string
     */
    function run()
    {
        try
        {
            $publication = $this->getAjaxComponent()->getContentObjectPublication();
            $users = $this->getUserOvertimeService()->getUsersByPublication($publication);
            return new JsonResponse(['users' => $users]);
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            return new JsonResponse(['error' => $ex->getMessage()], 500);
        }
    }
}
