<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SetMultipleUsersOvertimeComponent extends Manager
{
    /**
     * @return string
     */
    function run()
    {
        try
        {
            $data = $this->getRequest()->getFromPost(self::PARAM_DB_ACTIONS);
            $publication = $this->getAjaxComponent()->getContentObjectPublication();

            foreach($data as $d)
            {
                switch ($d[self::PARAM_DB_ACTION_TYPE])
                {
                    case 'create':
                        $userId = $d[self::PARAM_USER_ID];
                        $extraTime = $d[self::PARAM_EXTRA_TIME];
                        $this->getUserOvertimeService()->addUserOvertimeData($publication, (int) $userId, (int) $extraTime);
                        break;
                    case 'update':
                        $userOvertimeId = $d[self::PARAM_USER_OVERTIME_ID];
                        $extraTime = $d[self::PARAM_EXTRA_TIME];
                        $this->getUserOvertimeService()->updateUserOvertimeData((int) $userOvertimeId, $extraTime);
                        break;
                    case 'delete':
                        $userOvertimeId = $d[self::PARAM_USER_OVERTIME_ID];
                        $this->getUserOvertimeService()->deleteUserOvertimeData((int) $userOvertimeId);
                        break;
                }
            }

            $usersExtraTime = $this->getUserOvertimeService()->getUserOvertimeDataByPublication($publication);
            return new JsonResponse([self::PARAM_RESULTS => $usersExtraTime]);
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            return new JsonResponse([self::PARAM_ERROR_MESSAGE => $ex->getMessage()], 500);
        }
    }
}