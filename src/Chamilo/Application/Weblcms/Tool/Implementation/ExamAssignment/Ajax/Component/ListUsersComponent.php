<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
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
            $pid = $this->getRequest()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
            $publication = DataManager::retrieve_by_id(ContentObjectPublication::class_name(), $pid);
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
