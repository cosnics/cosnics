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
            $publicationId = json_decode($this->getRequest()->getFromPost(self::PARAM_PUBLICATION_ID));
            $userId = json_decode($this->getRequest()->getFromPost(self::PARAM_USER_ID));
            $extraTime = json_decode($this->getRequest()->getFromPost(self::PARAM_EXTRA_TIME));

            $publication = DataManager::retrieve_by_id(ContentObjectPublication::class_name(), $publicationId);

            $this->getUserOvertimeService()->addUserOvertimeData($publication, (int) $userId, (int) $extraTime);

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
