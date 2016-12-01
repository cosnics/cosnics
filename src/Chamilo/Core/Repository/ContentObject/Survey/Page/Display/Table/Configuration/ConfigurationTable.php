<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Table\Configuration;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ConfigurationTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_CONFIGURATION_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_CONFIGURATION)), 
                Translation::getInstance()->getTranslation('RemoveSelected', array(), Utilities::COMMON_LIBRARIES), 
                false));
        return $actions;
    }
}
?>