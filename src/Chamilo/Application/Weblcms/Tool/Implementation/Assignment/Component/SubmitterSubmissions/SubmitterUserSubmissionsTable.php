<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Table to browse a list of submissions for a certain user.
 *
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmitterUserSubmissionsTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: PARAM_SUBMISSION;
    const DEFAULT_NAME = 'submitter_user_submissions_table';

    /*
     * (non-PHPdoc) @see \libraries\format\TableFormActionsSupport::get_implemented_form_actions()
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);
        if ($this->get_component()->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager :: PARAM_ACTION => Manager :: ACTION_DOWNLOAD_SUBMISSIONS)),
                    Translation :: get('DownloadSelected'),
                    false));
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_SUBMISSION)),
                    Translation :: get('DeleteSelected')));
        }
        return $actions;
    }
}
