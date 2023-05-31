<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * Disables the student view for the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DisableStudentViewComponent extends Manager
{

    public function run()
    {
        $sessionVariable = $this->getStudentViewSessionVariable();
        $sessionValue = $this->getSessionUtilities()->get($sessionVariable);

        if (empty($sessionValue))
        {
            throw new NotAllowedException();
        }

        $this->getSessionUtilities()->unregister($sessionVariable);

        $this->redirectWithMessage('', false, [self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT]);
    }
}