<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * Disables the student view for the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DisableStudentViewComponent extends Manager
{

    function run()
    {
        $sessionVariable = $this->getStudentViewSessionVariable();
        $sessionValue = Session::get($sessionVariable);

        if(empty($sessionValue))
        {
            throw new NotAllowedException();
        }

        Session::unregister($sessionVariable);

        $this->redirectWithMessage('', false, array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT));
    }
}