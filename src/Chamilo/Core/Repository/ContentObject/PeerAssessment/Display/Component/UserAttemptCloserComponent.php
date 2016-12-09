<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;

class UserAttemptCloserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(self::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }
        
        $this->close_user_attempt(Request::get(self::PARAM_USER), Request::get(self::PARAM_ATTEMPT));
        
        $this->redirect(null, false, array(self::PARAM_ACTION => self::ACTION_VIEW_USER_ATTEMPT_STATUS));
    }
}
