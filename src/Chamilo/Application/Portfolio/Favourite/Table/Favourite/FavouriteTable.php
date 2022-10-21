<?php
namespace Chamilo\Application\Portfolio\Favourite\Table\Favourite;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table to render the favourite users
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouriteTable extends RecordTable implements TableActionsSupport
{

    /**
     * Returns the implemented form actions
     *
     * @return TableActions
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(Manager::context(), Manager::PARAM_FAVOURITE_ID);

        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_SOURCE => Manager::SOURCE_FAVOURITES_BROWSER
                    )
                ), Translation::getInstance()->getTranslation('DeleteSelected', null, StringUtilities::LIBRARIES), true
            )
        );

        return $actions;
    }
}