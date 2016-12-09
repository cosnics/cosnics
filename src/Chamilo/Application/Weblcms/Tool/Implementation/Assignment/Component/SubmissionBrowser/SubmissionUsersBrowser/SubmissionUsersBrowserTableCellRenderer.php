<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionUsersBrowser;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionBrowserTableCellRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package application.weblcms.tool.assignment.php.component.submission_browser This class is a cell renderer for a
 *          submitters browser table
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class SubmissionUsersBrowserTableCellRenderer extends SubmissionBrowserTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Renders a cell for a given record
     * 
     * @param $column \libraries\ObjectTableColumn
     *
     * @param mixed[string] $user
     *
     * @return String
     */
    public function render_cell($column, $user)
    {
        $user_submitter_id = $user[AssignmentSubmission::PROPERTY_SUBMITTER_ID];
        
        switch ($column->get_name())
        {
            case User::PROPERTY_LASTNAME :
                if ($user_submitter_id == $this->get_component()->get_user_id() ||
                     $this->get_component()->get_assignment()->get_visibility_submissions() == 1 ||
                     $this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
                {
                    $url = $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_component()->get_publication_id(), 
                            Manager::PARAM_TARGET_ID => $user_submitter_id, 
                            Manager::PARAM_SUBMITTER_TYPE => $this->get_submitter_type(), 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_BROWSE_SUBMISSIONS));
                    return '<a href=\'' . $url . '\'>' . $user[User::PROPERTY_LASTNAME] . ', ' .
                         $user[User::PROPERTY_FIRSTNAME] . '</a>';
                }
                return $user[User::PROPERTY_LASTNAME] . ', ' . $user[User::PROPERTY_FIRSTNAME];
        }
        
        return parent::render_cell($column, $user);
    }

    /**
     * Returns the submitter type for this table cell renderer
     * 
     * @return int
     */
    public function get_submitter_type()
    {
        return AssignmentSubmission::SUBMITTER_TYPE_USER;
    }

    /**
     * Returns whether or not the current user is the submitter or part of the submitter entity
     * 
     * @param int $submitter_id - the id of the submitter entity
     * @param int $user_id - the id of the logged in user
     * @return bool
     */
    public function is_submitter($submitter_id, $user_id)
    {
        return $submitter_id == $user_id;
    }
}
