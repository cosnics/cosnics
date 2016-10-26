<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Viewer for assignments
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class ViewerComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }

    /**
     * Adds toolbar items to the toolbar
     *
     * @return array ToolbarItems
     */
    public function get_tool_actions()
    {
        $actions = array();
        $publication_id = Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION);
        $actions[] = new Button(
            Translation :: get('SubmissionSubmit'),
            Theme :: getInstance()->getCommonImagePath('Action/Add'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_SUBMIT_SUBMISSION,
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication_id,
                    self :: PARAM_TARGET_ID => $this->get_user_id(),
                    self :: PARAM_SUBMITTER_TYPE => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER)),
            Button :: DISPLAY_ICON_AND_LABEL);
        return $actions;
    }
}
