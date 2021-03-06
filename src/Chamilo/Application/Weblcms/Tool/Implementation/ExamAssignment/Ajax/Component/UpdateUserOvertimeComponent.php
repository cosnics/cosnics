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
class UpdateUserOvertimeComponent extends Manager
{
    /**
     * @return string
     */
    function run()
    {
        try
        {
            $userOvertimeId = $this->getRequest()->getFromPost(self::PARAM_USER_OVERTIME_ID);
            $extraTime = $this->getRequest()->getFromPost(self::PARAM_EXTRA_TIME);

            $this->getUserOvertimeService()->updateUserOvertimeData((int) $userOvertimeId, (int) $extraTime);

            $publication = $this->getAjaxComponent()->getContentObjectPublication();
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
