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
 * Description of submitter_group_submissions_browser_table
 *
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmitterGroupSubmissionsTable extends DataClassTable implements TableFormActionsSupport
{
    const DEFAULT_NAME = 'submitter_group_submissions_table';

    /*
     * (non-PHPdoc) @see \libraries\format\TableFormActionsSupport::get_implemented_form_actions()
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        if ($this->get_component()->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $actions->add_form_action(
                new TableFormAction(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_DOWNLOAD_SUBMISSIONS),
                    Translation :: get('DownloadSelected'),
                    false));
            $actions->add_form_action(
                new TableFormAction(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_SUBMISSION),
                    Translation :: get('DeleteSelected')));
        }
        return $actions;
    }
}
