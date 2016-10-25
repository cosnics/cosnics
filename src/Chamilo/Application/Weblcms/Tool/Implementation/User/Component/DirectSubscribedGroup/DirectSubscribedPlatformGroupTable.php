<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\DirectSubscribedGroup;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Table to display a list of groups directly subscribed to a course.
 *
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class DirectSubscribedPlatformGroupTable extends RecordTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_OBJECTS;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the implemented form actions
     *
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        if ($this->get_component()->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);

            // unsubscribe
            $actions->add_form_action(

                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_UNSUBSCRIBE_GROUPS)),
                    Translation :: get('UnsubscribeSelectedGroups'),
                    false));

            // make teacher
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_CHANGE_PLATFORMGROUP_STATUS_TEACHER)),
                    Translation :: get('MakeTeacher'),
                    false));

            // make student
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_CHANGE_PLATFORMGROUP_STATUS_STUDENT)),
                    Translation :: get('MakeStudent'),
                    false));

            return $actions;
        }
    }
}