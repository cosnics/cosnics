<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Table for ephorus requests browser.
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class RequestTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_REQUEST_IDS;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);

        if ($this->get_component()->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $actions->add_form_action(

                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Manager :: PARAM_ACTION => Manager :: ACTION_INDEX_VISIBILITY_CHANGER)),
                    Translation :: get('ToggleIndexVisibility')));
        }

        return $actions;
    }

    public static function handle_table_action()
    {
        $ids = static :: get_selected_ids();
        \Chamilo\Libraries\Platform\Session\Request :: set_get(static :: TABLE_IDENTIFIER, $ids);

        $action = \Chamilo\Libraries\Platform\Session\Request :: get(
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION);
        if ($action == Manager :: ACTION_INDEX_VISIBILITY_CHANGER)
        {
            \Chamilo\Libraries\Platform\Session\Request :: set_get(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: PARAM_ACTION,
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: ACTION_CHANGE_INDEX_VISIBILITY);
        }
    }
}