<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Deletes all the attempts for a given tree node and user
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DeleteAttemptsForTreeNodeComponent extends BaseReportingComponent
{
    const PARAM_SOURCE = 'source';

    /**
     * Executes this component
     */
    public function run()
    {
        if (!$this->is_allowed_to_edit_attempt_data())
        {
            throw new NotAllowedException();
        }

        $parameters = $filters = [];

        $trackingService = $this->getTrackingService();
        $learningPath = $this->learningPath;
        $reportingUser = $this->getReportingUser();
        $treeNode = $this->getCurrentTreeNode();

        $source = $this->getRequest()->getFromRequestOrQuery(self::PARAM_SOURCE);

        $action = self::ACTION_VIEW_USER_PROGRESS;
        $childId = $treeNode->getId();

        if($source == self::ACTION_REPORTING)
        {
            $action = $source;

            $childId = $treeNode->getParentNode() instanceof TreeNode ?
                $treeNode->getParentNode()->getId() : $treeNode->getId();
        }
        else
        {
            $filters[] = self::PARAM_REPORTING_USER_ID;
        }

        $parameters[self::PARAM_ACTION] = $action;
        $parameters[self::PARAM_CHILD_ID] = $childId;

        try
        {
            $trackingService->deleteTreeNodeAttemptsForTreeNode(
                $learningPath, $this->getUser(), $reportingUser, $treeNode
            );

            $is_error = false;

            $message = Translation::get(
                'ObjectsDeleted',
                array('OBJECT' => Translation::get('LearningPathItemAttempt'), StringUtilities::LIBRARIES)
            );
        }
        catch (Exception $ex)
        {
            $is_error = true;

            $message = Translation::get(
                'ObjectsNotDeleted',
                array('OBJECT' => Translation::get('LearningPathItemAttempt'), StringUtilities::LIBRARIES)
            );
        }

        $this->redirectWithMessage($message, $is_error, $parameters, $filters);
    }

    function build()
    {
    }
}
