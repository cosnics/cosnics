<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Platform\Session\Request;

class AttemptCloserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->close_attempt(Request::get(self::PARAM_ATTEMPT));
        
        // TODO add message
        $this->redirect(null, false, array(self::PARAM_ACTION => self::ACTION_OVERVIEW_STATUS));
    }
}
