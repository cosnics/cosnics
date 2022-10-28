<?php
namespace Chamilo\Configuration\Category\Table\Browser;

use Chamilo\Configuration\Category\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.common.category_manager.component.category_browser
 */
/**
 * Table to display a set of learning objects.
 */
class CategoryTable extends DataClassListTableRenderer implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_CATEGORY_ID;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(Manager::context(), self::TABLE_IDENTIFIER);

        if ($this->get_component()->supports_impact_view())
        {
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_IMPACT_VIEW)),
                    Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES),
                    false));
        }
        else
        {
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_CATEGORY)),
                    Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)));
        }

        if ($this->get_component()->get_subcategories_allowed())
        {
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_CHANGE_CATEGORY_PARENT)),
                    Translation::get('MoveSelected', null, StringUtilities::LIBRARIES),
                    false));
        }

        // add check for VisibilitSupported marker interface
        if ($this->get_component()->category_visibility_supported())
        {
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_TOGGLE_CATEGORY_VISIBILITY)),
                    Translation::get('ToggleVisibility', null, StringUtilities::LIBRARIES)));
        }

        return $actions;
    }
}
