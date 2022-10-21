<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Table\Favourite;

use Chamilo\Core\Repository\Workspace\Favourite\Manager;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTable;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace\Personal
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteTable extends WorkspaceTable
{

    /**
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport::getTableActions()
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)), 
                Translation::get('DeleteSelected', null, StringUtilities::LIBRARIES),
                true));
        
        return $actions;
    }
}
