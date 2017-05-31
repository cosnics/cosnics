<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;

/**
 * Toggles the blocked status of a given step
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ToggleBlockedStatusComponent extends Manager
{
    /**
     * Runs this component and returns it's output
     */
    public function run()
    {
        if (!$this->canEditCurrentLearningPathTreeNode())
        {
            throw new NotAllowedException();
        }

        $this->validateSelectedLearningPathChild();

        $currentLearningPathTreeNode = $this->getCurrentLearningPathTreeNode();
        $learningPathChildService = $this->getLearningPathChildService();

        try
        {
            $learningPathChildService->toggleContentObjectBlockedStatus($currentLearningPathTreeNode);
            $success = true;
        }
        catch(\Exception $ex)
        {
            $success = false;
        }

        if($currentLearningPathTreeNode->getLearningPathChild()->isBlocked())
        {
            $translation = $success ? 'StepMarkedAsRequired' : 'StepNotMarkedAsRequired';
        }
        else
        {
            $translation = $success ? 'StepMarkedAsOptional' : 'StepNotMarkedAsOptional';
        }

        $this->redirect(
            Translation::getInstance()->getTranslation($translation),
            !$success,
            array(
                self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
            ),
            array(self::PARAM_CONTENT_OBJECT_ID)
        );
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_CHILD_ID);
    }
}