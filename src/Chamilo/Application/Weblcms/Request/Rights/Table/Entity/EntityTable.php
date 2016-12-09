<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Table\Entity;

use Chamilo\Application\Weblcms\Request\Rights\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class EntityTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID;

    function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)), 
                Translation::get('RemoveSelected', null, Utilities::COMMON_LIBRARIES)));
        return $actions;
    }
}
?>