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
class DeleteUserOvertimeComponent extends Manager
{
    /**
     * @return string
     */
    function run()
    {
        try
        {
            $publicationId = json_decode($this->getRequest()->getFromPost(self::PARAM_PUBLICATION_ID));
            $userOvertimeId = json_decode($this->getRequest()->getFromPost(self::PARAM_USER_OVERTIME_ID));
            $this->getUserOvertimeService()->deleteUserOvertimeData((int) $userOvertimeId);

            $publication = DataManager::retrieve_by_id(ContentObjectPublication::class_name(), $publicationId);
            $usersExtraTime = $this->getUserOvertimeService()->getUserOvertimeDataByPublication($publication);
            return new JsonResponse(['usersOvertime' => $usersExtraTime]);
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            return new JsonResponse(['error' => $ex->getMessage()], 500);
        }
    }
}
