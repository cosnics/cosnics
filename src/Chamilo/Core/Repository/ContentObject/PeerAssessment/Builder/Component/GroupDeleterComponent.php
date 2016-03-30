<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class GroupDeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(self :: EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $group_id = Request :: get(self :: PARAM_GROUP);

        $users = $this->get_group_users($group_id);

        if ($this->group_has_scores($group_id))
        {
            $this->redirect(
                Translation :: get('PeerAssessmentGroupAlreadyActive'),
                true,
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS));
        }

        if ($this->delete_group($group_id))
        {
            $this->redirect(
                Translation :: get(
                    'ObjectDeleted',
                    array('OBJECT' => Translation :: get('PeerAssessmentGroup')),
                    Utilities :: COMMON_LIBRARIES),
                false,
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS));
        }
        else
        {
            $this->redirect(
                Translation :: get('Error'),
                true,
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS));
        }
    }
}
