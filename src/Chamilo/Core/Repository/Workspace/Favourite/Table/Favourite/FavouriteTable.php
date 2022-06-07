<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Table\Favourite;

use Chamilo\Core\Repository\Workspace\Favourite\Manager;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
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
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport::get_implemented_form_actions()
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)), 
                Translation::get('DeleteSelected', null, StringUtilities::LIBRARIES),
                true));
        
        return $actions;
    }
}
