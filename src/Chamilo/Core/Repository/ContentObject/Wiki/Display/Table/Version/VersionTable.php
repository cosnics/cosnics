<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Table\Version;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\Translation;

class VersionTable extends \Chamilo\Core\Repository\Table\ContentObject\Version\VersionTable
{

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_HISTORY)), 
                Translation::get('CompareSelected'), 
                false));
        return $actions;
    }
}
