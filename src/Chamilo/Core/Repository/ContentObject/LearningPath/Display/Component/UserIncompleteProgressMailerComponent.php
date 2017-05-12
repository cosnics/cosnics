<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * Mails the users that do not have completed the given LearningPathTreeNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserIncompleteProgressMailerComponent extends Manager
{
    /**
     * Runs this component and returns its output
     *
     * @throws NotAllowedException
     */
    function run()
    {
        if(!$this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()))
        {
            throw new NotAllowedException();
        }

        $currentLearningPathTreeNode = $this->getCurrentLearningPathTreeNode();
        $learningPathTrackingService = $this->getLearningPathTrackingService();
    }
}