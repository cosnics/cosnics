<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * Shows the student view for the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ShowStudentViewComponent extends Manager
{
    function run()
    {
        if(!$this->canEditCurrentTreeNode())
        {
            throw new NotAllowedException();
        }

        $sessionVariable = $this->getStudentViewSessionVariable();
        Session::register($sessionVariable, 1);

        $this->redirectWithMessage('', false, array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT));
    }
}