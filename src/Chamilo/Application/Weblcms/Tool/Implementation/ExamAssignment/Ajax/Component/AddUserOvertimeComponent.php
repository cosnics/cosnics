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
class AddUserOvertimeComponent extends Manager
{
    /**
     * @return string
     */
    function run()
    {
        try
        {
            $userId = $this->getRequest()->getFromPost(self::PARAM_USER_ID);
            $extraTime = $this->getRequest()->getFromPost(self::PARAM_EXTRA_TIME);
            $publication = $this->getAjaxComponent()->getContentObjectPublication();
            
            $this->getUserOvertimeService()->addUserOvertimeData($publication, (int) $userId, (int) $extraTime);

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
