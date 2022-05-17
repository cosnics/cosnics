<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * Toggles the blocked status of a given step
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ToggleEnforceDefaultTraversingOrderComponent extends Manager
{
    /**
     * Runs this component and returns it's output
     */
    public function run()
    {
        if (!$this->canEditCurrentTreeNode())
        {
            throw new NotAllowedException();
        }

        $this->validateSelectedTreeNodeData();

        $currentTreeNode = $this->getCurrentTreeNode();
        $learningPathService = $this->getLearningPathService();

        try
        {
            $learningPathService->toggleEnforceDefaultTraversingOrder($currentTreeNode);
            $success = true;
        }
        catch (Exception $ex)
        {
            $success = false;
        }

        if ($currentTreeNode->getTreeNodeData()->enforcesDefaultTraversingOrder())
        {
            $translation = $success ? 'DefaultTraversingOrderEnforced' : 'DefaultTraversingOrderNotEnforced';
        }
        else
        {
            $translation = $success ? 'FreeTraversingOrderSet' : 'FreeTraversingOrderNotSet';
        }

        $this->redirect(
            Translation::getInstance()->getTranslation($translation), !$success, array(
                self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId()
            ), array(self::PARAM_CONTENT_OBJECT_ID)
        );
    }
}