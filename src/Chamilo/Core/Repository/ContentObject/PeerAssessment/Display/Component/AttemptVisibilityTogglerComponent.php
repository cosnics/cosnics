<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Platform\Session\Request;

class AttemptVisibilityTogglerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->toggle_attempt_visibility(Request::get(self::PARAM_ATTEMPT));
        
        $this->redirect(null, false, array(self::PARAM_ACTION => self::ACTION_BROWSE_ATTEMPTS));
    }
}
