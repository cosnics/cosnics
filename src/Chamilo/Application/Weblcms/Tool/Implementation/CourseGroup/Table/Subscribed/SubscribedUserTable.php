<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: course_group_subscribed_user_browser_table.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class SubscribedUserTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Application\Weblcms\Manager :: PARAM_USERS;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);
        $browser = $this->get_component();

        if ($browser->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_UNSUBSCRIBE)),
                    Translation :: get('UnsubscribeUsers')));
        }

        return $actions;
    }
}