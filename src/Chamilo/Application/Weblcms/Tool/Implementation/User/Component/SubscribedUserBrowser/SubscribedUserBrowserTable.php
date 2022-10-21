<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubscribedUserBrowser;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * Table to display a list of users in a course (direct subscribed or platform group).
 * 
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class SubscribedUserBrowserTable extends RecordTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_OBJECTS;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        if ($this->get_component()->is_course_admin($this->get_component()->get_user()))
        {
            // if we are not editing groups
            if (! Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP))
            {
                $actions->addAction(
                    new TableAction(
                        $this->get_component()->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_UNSUBSCRIBE)), 
                        Translation::get('UnsubscribeSelected'), 
                        false));
                
                // make teacher
                $actions->addAction(
                    new TableAction(
                        $this->get_component()->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_CHANGE_USER_STATUS_TEACHER)), 
                        Translation::get('MakeTeacher'), 
                        false));
                
                // make student
                $actions->addAction(
                    new TableAction(
                        $this->get_component()->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_CHANGE_USER_STATUS_STUDENT)), 
                        Translation::get('MakeStudent'), 
                        false));
            }
        }
        
        return $actions;
    }
}