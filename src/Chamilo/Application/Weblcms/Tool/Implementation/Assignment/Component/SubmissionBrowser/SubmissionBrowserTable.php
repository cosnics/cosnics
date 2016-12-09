<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Abstract table to browse a list of submitters.
 * 
 * @package application.weblcms.tool.assignment.php.component.submission_browser
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to RecordTable
 */
abstract class SubmissionBrowserTable extends RecordTable implements TableFormActionsSupport
{
    /**
     * The identifier for the table (used for table actions)
     */
    const TABLE_IDENTIFIER = Manager::PARAM_TARGET_ID;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the available table actions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD_SUBMISSIONS)), 
                    Translation::get('DownloadSubmissionsSelected'), 
                    false));
        }
        
        return $actions;
    }
}
