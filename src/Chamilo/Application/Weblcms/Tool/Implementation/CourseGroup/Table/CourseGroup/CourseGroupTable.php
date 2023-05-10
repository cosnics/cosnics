<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroup;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class CourseGroupTable extends DataClassListTableRenderer implements TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_COURSE_GROUP;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(Manager::context(), self::TABLE_IDENTIFIER);
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url([Manager::PARAM_ACTION => Manager::ACTION_DELETE_COURSE_GROUP]),
                    Translation::get('RemoveSelectedCourseGroups')
                )
            );
        }

        return $actions;
    }
}