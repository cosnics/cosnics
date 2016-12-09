<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class UserAttemptClosedTogglerComponent extends Manager
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
        
        $success = $this->toggle_attempt_status_close(
            Request::get(self::PARAM_USER), 
            Request::get(self::PARAM_ATTEMPT));
        
        $message = $success ? Translation::get('success') : Translation::get('error');
        $error = $success ? false : true;
        
        $this->redirect(null, $error, array(self::PARAM_ACTION => self::ACTION_OVERVIEW_STATUS));
    }
}
