<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * Changes the title of a given LearningPathTreeNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdateLearningPathTreeNodeTitleComponent extends Manager
{
    const PARAM_NEW_TITLE = 'new_title';

    /**
     * Executes this component and returns its output
     */
    public function run()
    {
        try
        {
            $learningPathTreeNode = $this->get_application()->getCurrentLearningPathTreeNode();
            $learningPathChildService = $this->get_application()->getLearningPathChildService();

            if (!$this->get_application()->canEditLearningPathTreeNode($learningPathTreeNode))
            {
                throw new NotAllowedException();
            }

            $learningPathChildService->updateContentObjectTitle(
                $learningPathTreeNode, $this->getRequestedPostDataValue(self::PARAM_NEW_TITLE)
            );

            JsonAjaxResult::success();
        }
        catch (\Exception $ex)
        {
            JsonAjaxResult::general_error();
        }
    }

    /**
     * Returns the required post parameters
     *
     * @return string
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_NEW_TITLE);
    }
}