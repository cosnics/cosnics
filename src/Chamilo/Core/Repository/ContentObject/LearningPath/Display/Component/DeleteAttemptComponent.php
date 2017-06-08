<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component to list activity on a portfolio item
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleteAttemptComponent extends BaseReportingComponent
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
        $learningPath = $this->get_root_content_object();
        $reportingUser = $this->getReportingUser();
        $treeNode = $this->getCurrentTreeNode();

        $parameters[self::PARAM_ACTION] = self::ACTION_REPORTING;
        $parameters[self::PARAM_CHILD_ID] = $this->getCurrentTreeNodeDataId();

        $item_attempt_id = $this->getRequest()->get(self::PARAM_ITEM_ATTEMPT_ID);

        try
        {
            if (isset($item_attempt_id))
            {
                $trackingService->deleteTreeNodeAttemptById(
                    $learningPath, $this->getUser(), $reportingUser, $treeNode, (int) $item_attempt_id
                );
            }
            else
            {
                $trackingService->deleteTreeNodeAttemptsForTreeNode(
                    $learningPath, $this->getUser(), $reportingUser, $treeNode
                );
            }

            $is_error = false;

            $message = Translation::get(
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
