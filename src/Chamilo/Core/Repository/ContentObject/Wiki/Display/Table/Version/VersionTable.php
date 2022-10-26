<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Table\Version;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Translation\Translation;

class VersionTable extends \Chamilo\Core\Repository\Table\ContentObject\Version\VersionTable
{

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_HISTORY)), 
                Translation::get('CompareSelected'), 
                false));
        return $actions;
    }
}
