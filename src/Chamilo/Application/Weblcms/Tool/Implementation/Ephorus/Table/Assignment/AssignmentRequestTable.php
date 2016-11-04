<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Assignment;

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
class AssignmentRequestTable extends DataClassTable implements TableFormActionsSupport
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
                            Manager :: PARAM_ACTION => Manager :: ACTION_INDEX_VISIBILITY_CHANGER
                        )
                    ),
                    Translation:: get('ToggleIndexVisibility'),
                    false
                )
            );

            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_ASSIGNMENT_EPHORUS_REQUEST
                        )
                    ),
                    Translation:: get('AddDocuments'),
                    false
                )
            );
        }

        return $actions;
    }
}
