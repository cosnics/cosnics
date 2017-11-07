<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * A table which represents all users which have portfolios published
 * 
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID;

    /**
     * Returns the implemented form actions
     * 
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(Manager::context(), Manager::PARAM_FAVOURITE_USER_ID);
        
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        \Chamilo\Application\Portfolio\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE_FAVOURITES, 
                        Manager::PARAM_ACTION => Manager::ACTION_CREATE)), 
                Translation::getInstance()->getTranslation('CreateFavourites', null, Manager::context()), 
                false));
        
        return $actions;
    }
}