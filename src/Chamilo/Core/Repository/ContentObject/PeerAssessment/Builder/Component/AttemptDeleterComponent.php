<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class AttemptDeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $attempt_id = Request :: get(self :: PARAM_ATTEMPT);
        
        if (! $this->has_scores($attempt_id))
        {
            $success = $this->delete_attempt($attempt_id);
            
            $message = $success ? Translation :: get('Succes') : Translation :: get('Error');
            $error = $success ? false : true;
        }
        else
        {
            $message = Translation :: get('AttemptAlreadyScored');
            $error = true;
        }
        
        $this->redirect($message, $error, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ATTEMPTS));
    }
}
