<?php
namespace Chamilo\Application\Weblcms\Request\Table\Request;

use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class RequestTable extends DataClassListTableRenderer implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_REQUEST_ID;
    const TYPE_PERSONAL = 1;
    const TYPE_PENDING = 2;
    const TYPE_GRANTED = 3;
    const TYPE_DENIED = 4;
    const DEFAULT_MAXIMUM_NUMBER_OF_RESULTS = 200;

    function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        if ($this->get_component()->get_user()->is_platform_admin())
        {
            if ($this->get_component()->get_table_type() == self::TYPE_PENDING ||
                 $this->get_component()->get_table_type() == self::TYPE_DENIED)
            {
                $actions->addAction(
                    new TableAction(
                        $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_GRANT)), 
                        Translation::get('GrantSelected', null, StringUtilities::LIBRARIES)));
            }
            
            if ($this->get_component()->get_table_type() == self::TYPE_PENDING)
            {
                $actions->addAction(
                    new TableAction(
                        $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DENY)), 
                        Translation::get('DenySelected', null, StringUtilities::LIBRARIES)));
            }
        }
        
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)), 
                Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)));
        
        return $actions;
    }
}