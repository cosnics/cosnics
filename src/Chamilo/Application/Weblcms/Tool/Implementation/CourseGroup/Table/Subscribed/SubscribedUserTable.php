<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class SubscribedUserTable extends RecordTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Application\Weblcms\Manager::PARAM_USERS;

    public function getTableActions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $browser = $this->get_component();

        if ($browser->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_GROUP_DETAILS)),
                    Translation::get('UnsubscribeUsers')));
        }

        return $actions;
    }
}