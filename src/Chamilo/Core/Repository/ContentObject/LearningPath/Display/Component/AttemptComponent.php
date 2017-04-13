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
class AttemptComponent extends Manager
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
        $parameters[self::PARAM_ACTION] = self::ACTION_REPORTING;

        $learningPathTrackingService = $this->getLearningPathTrackingService();
        $learningPath = $this->get_root_content_object();
        $user = $this->getUser();
        $learningPathTreeNode = $this->getCurrentLearningPathTreeNode();

        if ($this->isCurrentLearningPathChildIdSet())
        {
            $item_attempt_id = $this->getRequest()->get(self::PARAM_ITEM_ATTEMPT_ID);

            try
            {
                $parameters[self::PARAM_CHILD_ID] = $this->getCurrentLearningPathChildId();

                if (isset($item_attempt_id))
                {
                    $learningPathTrackingService->deleteLearningPathChildAttemptById(
                        $learningPath, $user, $learningPathTreeNode, (int) $item_attempt_id
                    );
                }
                else
                {
                    $learningPathTrackingService->deleteLearningPathChildAttemptsForLearningPathTreeNode(
                        $learningPath, $user, $learningPathTreeNode
                    );

                    if(!$learningPathTreeNode->isRootNode())
                    {
                        $parameters[self::PARAM_CHILD_ID] = $learningPathTreeNode->getParentNode()->getId();
                    }
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
        }
        else
        {
            try
            {
                $learningPathTrackingService->deleteLearningPathAttempt($learningPath, $user);

                $is_error = false;

                $message = Translation::get(
                    'ObjectDeleted',
                    array('OBJECT' => Translation::get('LearningPathAttempt'), Utilities::COMMON_LIBRARIES)
                );
            }
            catch (\Exception $ex)
            {
                $is_error = true;

                $message = Translation::get(
                    'ObjectNotDeleted',
                    array('OBJECT' => Translation::get('LearningPathAttempt'), Utilities::COMMON_LIBRARIES)
                );
            }

            $parameters[self::PARAM_CHILD_ID] = null;
        }

        $this->redirect($message, $is_error, $parameters);
    }
}
