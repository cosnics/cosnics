<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * Shows the student view for the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ShowStudentViewComponent extends Manager
{
    public function run()
    {
        if (!$this->canEditCurrentTreeNode())
        {
            throw new NotAllowedException();
        }

        $sessionVariable = $this->getStudentViewSessionVariable();
        $this->getSessionUtilities()->register($sessionVariable, 1);

        $this->redirectWithMessage('', false, [self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT]);
    }
}