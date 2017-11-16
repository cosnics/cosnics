<?php
namespace Chamilo\Application\Portfolio\Favourite\Table\Favourite;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table to render the favourite users
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouriteTable extends RecordTable implements TableFormActionsSupport
{

    /**
     * Returns the implemented form actions
     * 
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(Manager::context(), Manager::PARAM_FAVOURITE_ID);
        
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE, 
                        Manager::PARAM_SOURCE => Manager::SOURCE_FAVOURITES_BROWSER)), 
                Translation::getInstance()->getTranslation('DeleteSelected', null, Utilities::COMMON_LIBRARIES), 
                true));
        
        return $actions;
    }
}