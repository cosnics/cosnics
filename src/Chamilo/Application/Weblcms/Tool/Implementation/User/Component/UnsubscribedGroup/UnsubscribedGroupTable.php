<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\UnsubscribedGroup;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * * *************************************************************************** Table to display a list of groups not
 * in a course.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedGroupTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_OBJECTS;

    public function getTableActions(): TableActions
    {
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            // add subscribe options
            $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
            
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_SUBSCRIBE_GROUPS)), 
                    Translation::get('SubscribeSelectedGroups'), 
                    false));
            
            return $actions;
        }
    }
}