<?php
namespace Chamilo\Configuration\Category\Table\Browser;

use Chamilo\Configuration\Category\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: category_browser_table.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 *
 * @package application.common.category_manager.component.category_browser
 */
/**
 * Table to display a set of learning objects.
 */
class CategoryTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_CATEGORY_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);

        if ($this->get_component()->supports_impact_view())
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_IMPACT_VIEW)),
                    Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES),
                    false));
        }
        else
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_CATEGORY)),
                    Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));
        }

        if ($this->get_component()->get_subcategories_allowed())
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager :: PARAM_ACTION => Manager :: ACTION_CHANGE_CATEGORY_PARENT)),
                    Translation :: get('MoveSelected', null, Utilities :: COMMON_LIBRARIES),
                    false));
        }

        // add check for VisibilitSupported marker interface
        if ($this->get_component()->category_visibility_supported())
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager :: PARAM_ACTION => Manager :: ACTION_TOGGLE_CATEGORY_VISIBILITY)),
                    Translation :: get('ToggleVisibility', null, Utilities :: COMMON_LIBRARIES)));
        }

        return $actions;
    }
}
