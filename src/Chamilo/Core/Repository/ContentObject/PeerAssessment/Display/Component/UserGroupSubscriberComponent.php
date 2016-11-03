<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class UserGroupSubscriberComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! is_null(Request :: get(self :: PARAM_GROUP)))
        {
            $success = $this->add_user_to_group($this->get_user_id(), Request :: get(self :: PARAM_GROUP));
            $message = $success ? Translation :: get('GroupSubscriptionSucceeded') : Translation :: get(
                'NoGroupSubscription');
            $this->redirect(
                $message, 
                ! $success, 
                array(self :: PARAM_ACTION => self :: ACTION_VIEW_USER_ATTEMPT_STATUS));
        }
    }
}