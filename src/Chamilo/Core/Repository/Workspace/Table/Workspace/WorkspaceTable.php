<?php

namespace Chamilo\Core\Repository\Workspace\Table\Workspace;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_WORKSPACE_ID;

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport::get_implemented_form_actions()
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_FAVOURITE,
                        \Chamilo\Core\Repository\Workspace\Favourite\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Favourite\Manager::ACTION_CREATE,
                        Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                    )
                ),
                Translation::get('FavouriteSelected', null, Manager::context()),
                false
            )
        );

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                    )
                ),
                Translation::get('DeleteSelected', null, StringUtilities::LIBRARIES),
                true
            )
        );

        return $actions;
    }
}
