<?php

namespace Chamilo\Core\Repository\Workspace\Table\Workspace;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceTable extends DataClassListTableRenderer implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_WORKSPACE_ID;

    /**
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport::getTableActions()
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
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

        $actions->addAction(
            new TableAction(
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
