<?php
namespace Chamilo\Core\Repository\Table\Complex;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: complex_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component.complex_browser
 */
class ComplexTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Core\Repository\Builder\Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);

        $action = array(
            \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Builder\Manager :: ACTION_COPY_COMPLEX_CONTENT_OBJECT_ITEM);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url($action),
                Translation :: get('CopySelected', null, Utilities :: COMMON_LIBRARIES)),
            true);

        $action = array(
            \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Builder\Manager :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url($action),
                Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)),
            true);

        $action = array(
            \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Builder\Manager :: ACTION_CHANGE_PARENT);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url($action),
                Translation :: get('MoveSelected', null, Utilities :: COMMON_LIBRARIES),
                false));

        return $actions;
    }
}
