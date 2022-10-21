<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\DirectSubscribedPlatformGroupBrowser;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Table to display a list of groups directly subscribed to a course.
 * 
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class DirectSubscribedPlatformGroupBrowserTable extends RecordTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_OBJECTS;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the implemented form actions
     * 
     * @return TableActions
     */
    public function getTableActions(): TableActions
    {
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
            
            // unsubscribe
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_UNSUBSCRIBE_GROUPS)), 
                    Translation::get('UnsubscribeSelectedGroups'), 
                    false));
            
            // make teacher
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_CHANGE_PLATFORMGROUP_STATUS_TEACHER)), 
                    Translation::get('MakeTeacher'), 
                    false));
            
            // make student
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_CHANGE_PLATFORMGROUP_STATUS_STUDENT)), 
                    Translation::get('MakeStudent'), 
                    false));
            
            return $actions;
        }
    }
}