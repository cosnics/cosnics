<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Deletes a specific attempt for a tree node
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DeleteTreeNodeAttemptComponent extends BaseReportingComponent
{

    /**
     * Executes this component
     */
    public function run()
    {
        if (!$this->is_allowed_to_edit_attempt_data())
        {
            throw new NotAllowedException();
        }

        $parameters = array();

        $trackingService = $this->getTrackingService();
        $learningPath = $this->learningPath;
        $reportingUser = $this->getReportingUser();
        $treeNode = $this->getCurrentTreeNode();

        $parameters[self::PARAM_ACTION] = self::ACTION_REPORTING;
        $parameters[self::PARAM_CHILD_ID] = $this->getCurrentTreeNodeDataId();

        $item_attempt_id = $this->getRequest()->get(self::PARAM_ITEM_ATTEMPT_ID);
        if (empty($item_attempt_id))
        {
            throw new NoObjectSelectedException(Translation::getInstance()->getTranslation('Attempt'));
        }

        try
        {
            $trackingService->deleteTreeNodeAttemptById(
                $learningPath, $this->getUser(), $reportingUser, $treeNode, (int) $item_attempt_id
            );

            $is_error = false;

            $message = Translation::getInstance()->getTranslation(
                'ObjectDeleted',
                array('OBJECT' => Translation::get('LearningPathItemAttempt'), Utilities::COMMON_LIBRARIES)
            );
        }
        catch (\Exception $ex)
        {
            $is_error = true;

            $message = Translation::get(
                'ObjectNotDeleted',
                array('OBJECT' => Translation::get('LearningPathItemAttempt'), Utilities::COMMON_LIBRARIES)
            );
        }

        $this->redirect($message, $is_error, $parameters);
    }

    function build()
    {
    }
}
